<?php

namespace App\Http\Resources;

use App\Services\DTO\PricingResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    protected ?PricingResult $pricing = null;

    protected mixed $voucher = null;

    public function withPricing(PricingResult $pricing, mixed $voucher = null): self
    {
        $this->pricing = $pricing;
        $this->voucher = $voucher;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $items = $this->items()->with(['product.brand', 'product.primaryImage'])->get();
        $totalQty = $items->sum('qty');

        return [
            'id' => $this->id,
            'cart_token' => $this->cart_token,
            'currency' => $this->currency ?? 'IDR',
            'total_qty' => $totalQty,
            'items' => CartItemResource::collection($items),
            'pricing' => $this->pricing ? $this->pricing->toArray() : [
                'subtotal' => 0,
                'promo_discount' => 0,
                'new_user_discount' => 0,
                'voucher_discount' => 0,
                'total_discount' => 0,
                'handling_fee' => 0,
                'product_total' => 0,
            ],
            'voucher_applied' => $this->voucher ? [
                'code' => $this->voucher->code,
                'type' => $this->voucher->type?->value,
                'value' => $this->voucher->value,
            ] : null,
        ];
    }
}
