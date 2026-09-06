<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $hasDiscount = $product && $product->discount_price !== null && $product->discount_price > 0 && $product->discount_price < $product->price;
        $discountPercentage = ($hasDiscount && $product->price > 0)
            ? (int) round((1 - ($product->discount_price / $product->price)) * 100)
            : 0;

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $product?->name,
            'name' => $product?->name,
            'product_slug' => $product?->slug,
            'brand_name' => $product?->brand?->name,
            'image_path' => $product?->primaryImage?->path ?? $product?->images?->first()?->path,
            'qty' => $this->qty,
            'price' => $product?->price ?? 0,
            'discount_price' => $product?->discount_price,
            'unit_price' => $product?->final_price ?? 0,
            'has_discount' => $hasDiscount,
            'discount_percentage' => $discountPercentage,
            'subtotal' => ($product?->final_price ?? 0) * $this->qty,
            'stock' => $product?->stock ?? 0,
            'availability_type' => $product?->availability_type?->value,
            'is_in_stock' => $product?->availability_type?->value === 'open_po' || ($product?->stock ?? 0) >= $this->qty,
            'product' => [
                'id' => $product?->id,
                'name' => $product?->name,
                'slug' => $product?->slug,
                'brand_name' => $product?->brand?->name,
                'primary_image' => $product?->primaryImage?->path ?? $product?->images?->first()?->path,
                'price' => $product?->price ?? 0,
                'discount_price' => $product?->discount_price,
                'final_price' => $product?->final_price ?? 0,
                'unit_price' => $product?->final_price ?? 0,
                'has_discount' => $hasDiscount,
                'discount_percentage' => $discountPercentage,
                'stock' => $product?->stock ?? 0,
                'availability_type' => $product?->availability_type?->value,
            ],
        ];
    }
}
