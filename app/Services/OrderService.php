<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected OrderStatusService $orderStatusService
    ) {}

    public function resolveOosItem(Order $order, int $itemId, array $data, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $itemId, $data, $actor) {
            $item = $order->items()->findOrFail($itemId);

            $actorType = 'user';
            $actorId = $actor?->id;
            if ($actor && method_exists($actor, 'isAdmin') && $actor->isAdmin()) {
                $actorType = 'admin';
            }

            if ($data['resolution'] === 'refund') {
                $refundAmount = (int) ($data['amount'] ?? $item->subtotal);

                // SKEMA B: Cek apakah ada Invoice Tambahan yang belum dibayar (PENDING) pada pesanan ini
                $unpaidAdditionalInvoices = $order->invoices()
                    ->where('type', InvoiceType::ADDITIONAL)
                    ->where('status', InvoiceStatus::PENDING)
                    ->get();
                $totalUnpaidAdditional = (int) $unpaidAdditionalInvoices->sum('amount');

                if ($totalUnpaidAdditional > 0) {
                    if ($refundAmount >= $totalUnpaidAdditional) {
                        // Seluruh invoice tambahan lunas via kompensasi refund
                        foreach ($unpaidAdditionalInvoices as $inv) {
                            $inv->update([
                                'status' => InvoiceStatus::PAID,
                                'paid_at' => now(),
                                'payment_method' => PaymentMethod::MANUAL,
                                'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                'description' => $inv->description." (Lunas otomatis dipotong dari dana refund {$item->product_name_snapshot})",
                            ]);

                            Payment::create([
                                'invoice_id' => $inv->id,
                                'method' => PaymentMethod::MANUAL,
                                'status' => PaymentStatus::PAID,
                                'amount' => $inv->amount,
                                'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                'paid_at' => now(),
                                'raw_payload' => [
                                    'type' => 'internal_refund_offset',
                                    'note' => "Lunas otomatis dari kompensasi refund {$item->product_name_snapshot}",
                                    'offset_amount' => $inv->amount,
                                ],
                            ]);
                        }

                        $netRefund = $refundAmount - $totalUnpaidAdditional;

                        $item->update([
                            'refund_status' => $netRefund > 0 ? 'requested' : 'offset_settled',
                            'refund_amount' => $netRefund,
                        ]);

                        if ($netRefund > 0) {
                            Refund::create([
                                'order_id' => $order->id,
                                'reason' => "Barang habis di toko JP ({$item->product_name_snapshot}). Sisa dana setelah dipotong pelunasan invoice tambahan.",
                                'amount' => $netRefund,
                                'status' => RefundStatus::PENDING,
                                'refund_method' => 'bank_transfer',
                            ]);
                        }

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$item->product_name_snapshot} habis di JP. Dana refund Rp ".number_format($refundAmount, 0, ',', '.').' otomatis digunakan untuk melunasi tagihan Invoice Tambahan sebesar Rp '.number_format($totalUnpaidAdditional, 0, ',', '.').'.'.($netRefund > 0 ? ' Sisa dana refund yang diajukan ke pembeli: Rp '.number_format($netRefund, 0, ',', '.').'.' : ' Saldo refund impas.'),
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    } else {
                        // Dana refund tidak cukup melunasi seluruh invoice tambahan, potong sebagian
                        $remainingRefundToOffset = $refundAmount;
                        foreach ($unpaidAdditionalInvoices as $inv) {
                            if ($remainingRefundToOffset <= 0) {
                                break;
                            }
                            if ($inv->amount <= $remainingRefundToOffset) {
                                $remainingRefundToOffset -= $inv->amount;
                                $inv->update([
                                    'status' => InvoiceStatus::PAID,
                                    'paid_at' => now(),
                                    'payment_method' => PaymentMethod::MANUAL,
                                    'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                ]);
                                Payment::create([
                                    'invoice_id' => $inv->id,
                                    'method' => PaymentMethod::MANUAL,
                                    'status' => PaymentStatus::PAID,
                                    'amount' => $inv->amount,
                                    'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                    'paid_at' => now(),
                                    'raw_payload' => [
                                        'type' => 'internal_refund_offset',
                                        'offset_amount' => $inv->amount,
                                    ],
                                ]);
                            } else {
                                $inv->update([
                                    'amount' => $inv->amount - $remainingRefundToOffset,
                                    'description' => $inv->description.' (Dipotong Rp '.number_format($remainingRefundToOffset, 0, ',', '.')." dari refund {$item->product_name_snapshot})",
                                ]);
                                $remainingRefundToOffset = 0;
                            }
                        }

                        $item->update([
                            'refund_status' => 'offset_settled',
                            'refund_amount' => 0,
                        ]);

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$item->product_name_snapshot} habis di JP. Seluruh hak refund sebesar Rp ".number_format($refundAmount, 0, ',', '.').' dialihkan penuh untuk memotong tagihan Invoice Tambahan (Sisa tagihan: Rp '.number_format($totalUnpaidAdditional - $refundAmount, 0, ',', '.').').',
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    }
                } else {
                    // Alur biasa (belum ada invoice tambahan yang belum lunas)
                    $item->update([
                        'refund_status' => 'requested',
                        'refund_amount' => $refundAmount,
                    ]);

                    Refund::create([
                        'order_id' => $order->id,
                        'reason' => "Barang habis di toko JP ({$item->product_name_snapshot}). Pengembalian dana (partial refund).",
                        'amount' => $refundAmount,
                        'status' => RefundStatus::PENDING,
                        'refund_method' => 'bank_transfer',
                    ]);

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'from_status' => $order->status,
                        'to_status' => $order->status,
                        'note' => "Barang {$item->product_name_snapshot} habis di JP. Pengembalian dana seharga barang sebesar Rp ".number_format($refundAmount, 0, ',', '.').' berhasil diajukan.',
                        'actor_type' => $actorType,
                        'actor_id' => $actorId,
                    ]);
                }
            } elseif ($data['resolution'] === 'replace') {
                $oldName = $item->product_name_snapshot;
                $oldPrice = (int) $item->unit_price;
                $newPrice = (int) ($data['replacement_price'] ?? 0);
                $newName = $data['replacement_name'] ?? 'Produk Pengganti';
                $priceDiff = ($newPrice - $oldPrice) * $item->qty;

                if ($priceDiff > 0) {
                    // SKEMA B: Cek apakah ada dana refund pending yang dapat dikompensasikan (offset)
                    $pendingRefunds = $order->refunds()->where('status', RefundStatus::PENDING)->get();
                    $totalPendingRefund = (int) $pendingRefunds->sum('amount');

                    if ($totalPendingRefund >= $priceDiff) {
                        // Seluruh selisih tertutupi oleh saldo refund yang ada!
                        $remainingToDeduct = $priceDiff;
                        foreach ($pendingRefunds as $ref) {
                            if ($remainingToDeduct <= 0) {
                                break;
                            }
                            if ($ref->amount <= $remainingToDeduct) {
                                $remainingToDeduct -= $ref->amount;
                                $ref->update([
                                    'amount' => 0,
                                    'status' => RefundStatus::COMPLETED,
                                    'reason' => $ref->reason.' (Dialihkan penuh Rp '.number_format($ref->amount, 0, ',', '.')." untuk kompensasi selisih ganti produk {$newName})",
                                ]);
                            } else {
                                $ref->update([
                                    'amount' => $ref->amount - $remainingToDeduct,
                                    'reason' => $ref->reason.' (Dipotong Rp '.number_format($remainingToDeduct, 0, ',', '.')." untuk kompensasi selisih ganti produk {$newName})",
                                ]);
                                $remainingToDeduct = 0;
                            }
                        }

                        // Buat Invoice Tambahan langsung berstatus PAID (Lunas otomatis via offset)
                        $additionalInvoice = $this->invoiceService->createAdditionalInvoice($order, [
                            'amount' => $priceDiff,
                            'description' => "Tagihan Selisih Ganti Produk: {$newName} (Pengganti {$oldName}) - Lunas dipotong dari saldo refund",
                        ], $actor);

                        $additionalInvoice->update([
                            'status' => InvoiceStatus::PAID,
                            'paid_at' => now(),
                            'payment_method' => PaymentMethod::MANUAL,
                            'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                        ]);

                        Payment::create([
                            'invoice_id' => $additionalInvoice->id,
                            'method' => PaymentMethod::MANUAL,
                            'status' => PaymentStatus::PAID,
                            'amount' => $priceDiff,
                            'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                            'paid_at' => now(),
                            'raw_payload' => [
                                'type' => 'internal_refund_offset',
                                'note' => "Lunas otomatis dari pemotongan saldo refund barang habis pesanan #{$order->order_number}",
                                'offset_amount' => $priceDiff,
                            ],
                        ]);

                        $item->update([
                            'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                            'unit_price' => $newPrice,
                            'original_price' => $newPrice,
                            'subtotal' => $newPrice * $item->qty,
                            'refund_status' => null,
                            'refund_amount' => null,
                        ]);

                        $remainingRefundBalance = $totalPendingRefund - $priceDiff;
                        $this->syncOrderItemsRefundAmount($order, $remainingRefundBalance);

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$oldName} habis di JP. Diganti ke {$newName} (Selisih +Rp ".number_format($priceDiff, 0, ',', '.').'). Selisih lunas otomatis dipotong dari dana refund barang habis. Sisa dana refund Anda: Rp '.number_format($remainingRefundBalance, 0, ',', '.').'.',
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    } elseif ($totalPendingRefund > 0) {
                        // Saldo refund menutupi sebagian dari selisih biaya
                        $deficit = $priceDiff - $totalPendingRefund;

                        foreach ($pendingRefunds as $ref) {
                            $ref->update([
                                'amount' => 0,
                                'status' => RefundStatus::COMPLETED,
                                'reason' => $ref->reason.' (Dialihkan penuh Rp '.number_format($ref->amount, 0, ',', '.')." untuk kompensasi selisih ganti produk {$newName})",
                            ]);
                        }

                        // Buat Invoice Tambahan hanya untuk sisa kekurangannya
                        $this->invoiceService->createAdditionalInvoice($order, [
                            'amount' => $deficit,
                            'description' => "Tagihan Selisih Ganti Produk: {$newName} (Pengganti {$oldName}) - Setelah dipotong saldo refund Rp ".number_format($totalPendingRefund, 0, ',', '.'),
                        ], $actor);

                        $item->update([
                            'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                            'unit_price' => $newPrice,
                            'original_price' => $newPrice,
                            'subtotal' => $newPrice * $item->qty,
                            'refund_status' => null,
                            'refund_amount' => null,
                        ]);

                        $this->syncOrderItemsRefundAmount($order, 0);

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$oldName} habis di JP. Diganti ke {$newName} (Total selisih +Rp ".number_format($priceDiff, 0, ',', '.').'). Saldo refund Rp '.number_format($totalPendingRefund, 0, ',', '.').' otomatis digunakan sebagai pemotong tagihan. Diterbitkan Invoice Tambahan untuk sisa kekurangan sebesar Rp '.number_format($deficit, 0, ',', '.').'.',
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    } else {
                        // Tidak ada saldo refund -> Buat Invoice Tambahan biasa
                        $this->invoiceService->createAdditionalInvoice($order, [
                            'amount' => $priceDiff,
                            'description' => "Tagihan Selisih Ganti Produk: {$newName} (Pengganti {$oldName})",
                        ], $actor);

                        $item->update([
                            'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                            'unit_price' => $newPrice,
                            'original_price' => $newPrice,
                            'subtotal' => $newPrice * $item->qty,
                            'refund_status' => null,
                            'refund_amount' => null,
                        ]);

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$oldName} habis di JP. Diganti ke {$newName}. Diterbitkan Invoice Tambahan sebesar Rp ".number_format($priceDiff, 0, ',', '.').'.',
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    }
                } elseif ($priceDiff < 0) {
                    $refundAmount = abs($priceDiff);

                    // Cek apakah ada unpaid additional invoice yang bisa dioffset oleh kelebihan uang pergantian barang ini
                    $unpaidAdditionalInvoices = $order->invoices()
                        ->where('type', InvoiceType::ADDITIONAL)
                        ->where('status', InvoiceStatus::PENDING)
                        ->get();
                    $totalUnpaidAdditional = (int) $unpaidAdditionalInvoices->sum('amount');

                    if ($totalUnpaidAdditional > 0) {
                        if ($refundAmount >= $totalUnpaidAdditional) {
                            foreach ($unpaidAdditionalInvoices as $inv) {
                                $inv->update([
                                    'status' => InvoiceStatus::PAID,
                                    'paid_at' => now(),
                                    'payment_method' => PaymentMethod::MANUAL,
                                    'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                    'description' => $inv->description." (Lunas otomatis dipotong dari kelebihan pergantian barang {$newName})",
                                ]);
                                Payment::create([
                                    'invoice_id' => $inv->id,
                                    'method' => PaymentMethod::MANUAL,
                                    'status' => PaymentStatus::PAID,
                                    'amount' => $inv->amount,
                                    'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                    'paid_at' => now(),
                                    'raw_payload' => [
                                        'type' => 'internal_refund_offset',
                                        'offset_amount' => $inv->amount,
                                    ],
                                ]);
                            }

                            $netRefund = $refundAmount - $totalUnpaidAdditional;

                            $item->update([
                                'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                                'unit_price' => $newPrice,
                                'original_price' => $newPrice,
                                'subtotal' => $newPrice * $item->qty,
                                'refund_status' => $netRefund > 0 ? 'requested' : 'offset_settled',
                                'refund_amount' => $netRefund,
                            ]);

                            if ($netRefund > 0) {
                                Refund::create([
                                    'order_id' => $order->id,
                                    'reason' => "Kelebihan pembayaran pergantian barang {$oldName} ke {$newName} setelah dipotong invoice tambahan",
                                    'amount' => $netRefund,
                                    'status' => RefundStatus::PENDING,
                                    'refund_method' => 'bank_transfer',
                                ]);
                            }

                            OrderStatusHistory::create([
                                'order_id' => $order->id,
                                'from_status' => $order->status,
                                'to_status' => $order->status,
                                'note' => "Barang {$oldName} diganti ke {$newName}. Kelebihan dana Rp ".number_format($refundAmount, 0, ',', '.').' dipotong untuk melunasi tagihan Invoice Tambahan sebesar Rp '.number_format($totalUnpaidAdditional, 0, ',', '.').'.'.($netRefund > 0 ? ' Sisa kelebihan dana yang diajukan refund: Rp '.number_format($netRefund, 0, ',', '.').'.' : ' Saldo impas.'),
                                'actor_type' => $actorType,
                                'actor_id' => $actorId,
                            ]);
                        } else {
                            $remainingRefundToOffset = $refundAmount;
                            foreach ($unpaidAdditionalInvoices as $inv) {
                                if ($remainingRefundToOffset <= 0) {
                                    break;
                                }
                                if ($inv->amount <= $remainingRefundToOffset) {
                                    $remainingRefundToOffset -= $inv->amount;
                                    $inv->update([
                                        'status' => InvoiceStatus::PAID,
                                        'paid_at' => now(),
                                        'payment_method' => PaymentMethod::MANUAL,
                                        'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                    ]);
                                    Payment::create([
                                        'invoice_id' => $inv->id,
                                        'method' => PaymentMethod::MANUAL,
                                        'status' => PaymentStatus::PAID,
                                        'amount' => $inv->amount,
                                        'gateway_reference' => 'OFFSET-REFUND-'.$order->id,
                                        'paid_at' => now(),
                                        'raw_payload' => [
                                            'type' => 'internal_refund_offset',
                                            'offset_amount' => $inv->amount,
                                        ],
                                    ]);
                                } else {
                                    $inv->update([
                                        'amount' => $inv->amount - $remainingRefundToOffset,
                                        'description' => $inv->description.' (Dipotong Rp '.number_format($remainingRefundToOffset, 0, ',', '.').' dari kelebihan ganti barang)',
                                    ]);
                                    $remainingRefundToOffset = 0;
                                }
                            }

                            $item->update([
                                'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                                'unit_price' => $newPrice,
                                'original_price' => $newPrice,
                                'subtotal' => $newPrice * $item->qty,
                                'refund_status' => 'offset_settled',
                                'refund_amount' => 0,
                            ]);

                            OrderStatusHistory::create([
                                'order_id' => $order->id,
                                'from_status' => $order->status,
                                'to_status' => $order->status,
                                'note' => "Barang {$oldName} diganti ke {$newName}. Kelebihan dana Rp ".number_format($refundAmount, 0, ',', '.').' dialihkan penuh untuk memotong tagihan Invoice Tambahan (Sisa tagihan: Rp '.number_format($totalUnpaidAdditional - $refundAmount, 0, ',', '.').').',
                                'actor_type' => $actorType,
                                'actor_id' => $actorId,
                            ]);
                        }
                    } else {
                        // Tidak ada unpaid invoice tambahan -> Refund biasa
                        $item->update([
                            'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                            'unit_price' => $newPrice,
                            'original_price' => $newPrice,
                            'subtotal' => $newPrice * $item->qty,
                            'refund_status' => 'requested',
                            'refund_amount' => $refundAmount,
                        ]);

                        Refund::create([
                            'order_id' => $order->id,
                            'reason' => "Kelebihan pembayaran pergantian barang {$oldName} ke {$newName}",
                            'amount' => $refundAmount,
                            'status' => RefundStatus::PENDING,
                            'refund_method' => 'bank_transfer',
                        ]);

                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'from_status' => $order->status,
                            'to_status' => $order->status,
                            'note' => "Barang {$oldName} habis di JP. Diganti ke {$newName}. Kelebihan pembayaran sebesar Rp ".number_format($refundAmount, 0, ',', '.').' otomatis diajukan refund kepada pembeli.',
                            'actor_type' => $actorType,
                            'actor_id' => $actorId,
                        ]);
                    }
                } else {
                    $item->update([
                        'product_name_snapshot' => "{$newName} (Pengganti {$oldName})",
                        'unit_price' => $newPrice,
                        'original_price' => $newPrice,
                        'subtotal' => $newPrice * $item->qty,
                        'refund_status' => null,
                        'refund_amount' => null,
                    ]);

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'from_status' => $order->status,
                        'to_status' => $order->status,
                        'note' => "Barang {$oldName} habis di JP. Diganti ke {$newName} (Harga sama, tanpa penyesuaian biaya).",
                        'actor_type' => $actorType,
                        'actor_id' => $actorId,
                    ]);
                }
            }

            // Recalculate totals
            $order->product_subtotal = (int) $order->items()->sum('subtotal');
            $order->product_total = max(0, $order->product_subtotal - $order->product_discount_amount - $order->new_user_discount_amount - $order->voucher_amount + $order->handling_fee_amount);
            $order->grand_total = $order->product_total + ($order->shipping_total ?? 0);
            $order->save();

            return $order->fresh(['items', 'invoices.payments', 'statusHistories', 'trip', 'shipment', 'refunds']);
        });
    }

    protected function syncOrderItemsRefundAmount(Order $order, int $remainingRefundBalance): void
    {
        $refundItems = $order->items()
            ->whereIn('refund_status', ['requested', 'offset_settled'])
            ->get();

        $left = $remainingRefundBalance;
        foreach ($refundItems as $it) {
            if ($left <= 0) {
                $it->update([
                    'refund_amount' => 0,
                    'refund_status' => 'offset_settled',
                ]);
            } else {
                $currentPortion = (int) ($it->refund_amount ?: $it->subtotal);
                $allocated = min($left, $currentPortion);
                $it->update([
                    'refund_amount' => $allocated,
                    'refund_status' => 'requested',
                ]);
                $left -= $allocated;
            }
        }
    }
}
