<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Map of legal status transitions.
     */
    protected array $allowedTransitions = [
        OrderStatus::DRAFT->value => [
            OrderStatus::PENDING_PAYMENT_PRODUCT->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::PENDING_PAYMENT_PRODUCT->value => [
            OrderStatus::PAID_PRODUCT->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::PAID_PRODUCT->value => [
            OrderStatus::PROCESSING->value,
            OrderStatus::PACKING->value,
            OrderStatus::READY_FOR_DELIVERY->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::PROCESSING->value => [
            OrderStatus::PACKING->value,
            OrderStatus::READY_FOR_DELIVERY->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::PACKING->value => [
            OrderStatus::READY_FOR_DELIVERY->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::READY_FOR_DELIVERY->value => [
            OrderStatus::PENDING_PAYMENT_SHIPPING->value,
            OrderStatus::SHIPPING_PAID->value,
            OrderStatus::DELIVERING->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::PENDING_PAYMENT_SHIPPING->value => [
            OrderStatus::SHIPPING_PAID->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::SHIPPING_PAID->value => [
            OrderStatus::DELIVERING->value,
            OrderStatus::COMPLETED->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::DELIVERING->value => [
            OrderStatus::COMPLETED->value,
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::REFUND_REQUESTED->value => [
            OrderStatus::REFUNDED->value,
            OrderStatus::PAID_PRODUCT->value,
            OrderStatus::SHIPPING_PAID->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::COMPLETED->value => [
            OrderStatus::REFUND_REQUESTED->value,
        ],
        OrderStatus::CANCELLED->value => [],
        OrderStatus::REFUNDED->value => [],
    ];

    public function transition(Order $order, OrderStatus $to, ?string $note = null, ?Model $actor = null): Order
    {
        $from = $order->status;

        if ($from === $to) {
            return $order;
        }

        if (! $this->canTransition($from, $to)) {
            throw new BusinessException("Transisi status dari '{$from->value}' ke '{$to->value}' tidak diizinkan.");
        }

        return DB::transaction(function () use ($order, $from, $to, $note, $actor) {
            $actorType = 'system';
            $actorId = null;

            if ($actor instanceof User) {
                $actorId = $actor->id;
                $actorType = $actor->isAdmin() ? 'admin' : 'user';
            } elseif ($actor) {
                $actorId = $actor->getKey();
                $actorType = strtolower(class_basename($actor));
            }

            $updateData = ['status' => $to];

            if ($to === OrderStatus::PAID_PRODUCT && ! $order->product_paid_at) {
                $updateData['product_paid_at'] = now();
            } elseif ($to === OrderStatus::SHIPPING_PAID && ! $order->shipping_paid_at) {
                $updateData['shipping_paid_at'] = now();
            } elseif ($to === OrderStatus::COMPLETED && ! $order->completed_at) {
                $updateData['completed_at'] = now();
            } elseif ($to === OrderStatus::CANCELLED && ! $order->cancelled_at) {
                $updateData['cancelled_at'] = now();
                $updateData['cancel_reason'] = $note;
            }

            $order->update($updateData);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $from->value,
                'to_status' => $to->value,
                'note' => $note,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
            ]);

            event(new OrderStatusChanged($order, $from, $to, $note));

            return $order->fresh();
        });
    }

    public function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        $allowed = $this->allowedTransitions[$from->value] ?? [];

        return in_array($to->value, $allowed, true);
    }
}
