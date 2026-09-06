<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
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
            'flash_sale_start_at' => $this->flash_sale_start_at?->toIso8601String(),
            'flash_sale_end_at' => $this->flash_sale_end_at?->toIso8601String(),
            'weight_gram' => $this->weight_gram,
            'length_cm' => $this->length_cm,
            'width_cm' => $this->width_cm,
            'height_cm' => $this->height_cm,
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
                'logo_path' => $this->brand?->logo_path,
            ],
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'primary_image' => $this->primaryImage?->path ?? $this->images->first()?->path,
            'images' => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'is_primary' => (bool) $img->is_primary,
            ]),
            'reviews_count' => $this->reviews()->where('status', 'published')->count(),
            'average_rating' => (float) ($this->reviews()->where('status', 'published')->avg('rating') ?? 0),
        ];
    }
}
