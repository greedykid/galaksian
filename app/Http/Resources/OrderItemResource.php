<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $name = $this->product_name_snapshot;
        $isReplacement = str_contains($name, ' (Pengganti ');
        $cleanName = $name;
        $replacedName = null;

        if ($isReplacement) {
            $parts = explode(' (Pengganti ', $name);
            $cleanName = $parts[0];
            $replacedName = preg_replace('/\)$/', '', $parts[1] ?? '');
        }

        $image = $this->product?->primaryImage?->path
            ?? $this->product?->images?->first()?->path
            ?? null;

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->product_name_snapshot,
            'product_name' => $this->product_name_snapshot,
            'clean_name' => $cleanName,
            'is_replacement' => $isReplacement,
            'replaced_product_name' => $replacedName,
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
            'image' => $image,
        ];
    }
}
