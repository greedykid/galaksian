<?php

namespace App\Services;

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

        return DB::transaction(function () use ($order, $data, $amount) {
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
