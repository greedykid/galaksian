<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->product_name_snapshot,
            'product_name' => $this->product_name_snapshot,
            'brand' => $this->brand_name_snapshot,
            'brand_name' => $this->brand_name_snapshot,
            'sku' => $this->sku_snapshot,
            'qty' => $this->qty,
            'original_price' => $this->original_price,
            'unit_price' => $this->unit_price,
            'price' => $this->unit_price,
            'discount_amount' => $this->discount_amount,
            'subtotal' => $this->subtotal,
            'availability_type' => $this->availability_type?->value,
            'refund_status' => $this->refund_status,
            'refund_amount' => $this->refund_amount,
        ];
    }
}
