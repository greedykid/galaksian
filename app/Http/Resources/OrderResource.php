<?php

namespace App\Http\Resources;

use App\Enums\InvoiceStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'product_subtotal' => $this->product_subtotal,
            'product_discount_amount' => $this->product_discount_amount,
            'new_user_discount_amount' => $this->new_user_discount_amount,
            'voucher_amount' => $this->voucher_amount,
            'handling_fee_amount' => $this->handling_fee_amount,
            'product_total' => $this->product_total,
            'shipping_jastip_amount' => $this->shipping_jastip_amount,
            'shipping_local_amount' => $this->shipping_local_amount,
            'shipping_total' => $this->shipping_total,
            'grand_total' => $this->grand_total,
            'items_count' => $this->items()->count(),
            'total_qty' => $this->items()->sum('qty'),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'can_reorder' => $this->canReorder(),
            'has_pending_invoice' => $this->invoices()->where('status', InvoiceStatus::PENDING)->exists(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
