<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'final_price' => $this->final_price,
            'has_discount' => $this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price,
            'stock' => $this->stock,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_in_stock' => $this->availability_type->value === 'open_po' || $this->stock > 0,
            'availability_type' => $this->availability_type?->value,
            'origin_country' => $this->origin_country,
            'currency' => $this->currency,
            'is_active' => (bool) $this->is_active,
            'is_flash_sale' => $this->isCurrentlyFlashSale(),
            'flash_sale_end_at' => $this->flash_sale_end_at?->toIso8601String(),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'primary_image' => $this->primaryImage?->path ?? $this->images->first()?->path,
            'images' => $this->whenLoaded('images', fn () => $this->images->pluck('path')),
        ];
    }
}
