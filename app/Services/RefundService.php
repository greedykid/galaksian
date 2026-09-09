<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function createRefund(Order $order, array $data): Refund
    {
        $amount = (int) ($data['amount'] ?? $order->product_total);

        if ($amount <= 0) {
            throw new BusinessException('Nominal refund harus lebih dari 0.');
        }

        $maxAllowed = (int) ($order->grand_total ?: $order->product_total);
        if ($amount > $maxAllowed) {
            throw new BusinessException("Nominal refund tidak boleh melebihi total pesanan ({$maxAllowed}).");
        }

        // Refund hanya boleh diajukan untuk pesanan yang sudah dibayar (produk atau ongkir)
        $refundableStatuses = [
            OrderStatus::PAID_PRODUCT,
            OrderStatus::PROCESSING,
            OrderStatus::PACKING,
            OrderStatus::READY_FOR_DELIVERY,
            OrderStatus::PENDING_PAYMENT_SHIPPING,
            OrderStatus::SHIPPING_PAID,
            OrderStatus::DELIVERING,
            OrderStatus::COMPLETED,
        ];
        if (! in_array($order->status, $refundableStatuses, true)) {
            throw new BusinessException('Pesanan belum dapat direfund pada status saat ini.');
        }

        return DB::transaction(function () use ($order, $data, $amount) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $paidTotal = (int) $order->invoices()->where('status', InvoiceStatus::PAID)->sum('amount');
            if ($paidTotal === 0) {
                $paidTotal = (int) ($order->grand_total ?: $order->product_total);
            }
            $existingRefunds = (int) $order->refunds()->whereIn('status', [RefundStatus::PENDING, RefundStatus::APPROVED, RefundStatus::COMPLETED])->sum('amount');
            if ($amount > max(0, $paidTotal - $existingRefunds)) {
                throw new BusinessException('Nominal refund melebihi sisa dana yang dapat dikembalikan.');
            }

            if (! empty($data['invoice_id'])) {
                $invoice = $order->invoices()->whereKey($data['invoice_id'])->first();
                if (! $invoice || $invoice->status !== InvoiceStatus::PAID) {
                    throw new BusinessException('Invoice refund tidak valid.');
                }
            }

            $refund = Refund::create([
                'order_id' => $order->id,
                'invoice_id' => $data['invoice_id'] ?? null,
                'reason' => $data['reason'] ?? 'Pengajuan refund produk/pesanan.',
                'amount' => $amount,
                'status' => RefundStatus::PENDING,
                'refund_method' => $data['refund_method'] ?? 'bank_transfer',
                'evidence_path' => $data['evidence_path'] ?? null,
            ]);

            $this->orderStatusService->transition(
                $order,
                OrderStatus::REFUND_REQUESTED,
                'Pengajuan refund dibuat sebesar Rp '.number_format($amount, 0, ',', '.').". Alasan: {$refund->reason}"
            );

            return $refund;
        });
    }

    public function approveRefund(Refund $refund, User $admin): Refund
    {
        return DB::transaction(function () use ($refund, $admin) {
            $refund = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();
            if ($refund->status !== RefundStatus::PENDING) {
                throw new BusinessException('Refund hanya dapat disetujui saat berstatus pending.');
            }
            $refund->update([
                'status' => RefundStatus::COMPLETED,
                'approved_by' => $admin->id,
            ]);

            $order = $refund->order;

            $this->orderStatusService->transition(
                $order,
                OrderStatus::REFUNDED,
                'Refund sebesar Rp '.number_format($refund->amount, 0, ',', '.').' disetujui oleh admin.',
                $admin
            );

            return $refund;
        });
    }

    public function rejectRefund(Refund $refund, User $admin, string $reason): Refund
    {
        return DB::transaction(function () use ($refund, $admin, $reason) {
            $refund = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();
            if ($refund->status !== RefundStatus::PENDING) {
                throw new BusinessException('Refund hanya dapat ditolak saat berstatus pending.');
            }
            $refund->update([
                'status' => RefundStatus::REJECTED,
                'approved_by' => $admin->id,
            ]);

            $order = $refund->order;

            // Kembalikan ke status paid_product atau shipping_paid
            $targetStatus = $order->shipping_paid_at ? OrderStatus::SHIPPING_PAID : OrderStatus::PAID_PRODUCT;

            $this->orderStatusService->transition(
                $order,
                $targetStatus,
                "Pengajuan refund ditolak oleh admin. Alasan: {$reason}",
                $admin
            );

            return $refund;
        });
    }
}
