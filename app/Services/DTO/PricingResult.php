<?php

namespace App\Services\DTO;

class PricingResult
{
    public function __construct(
        public int $subtotal = 0,
        public int $promoDiscount = 0,
        public int $newUserDiscount = 0,
        public int $voucherDiscount = 0,
        public int $handlingFee = 0,
        public int $productTotal = 0,
        public int $shippingJastipAmount = 0,
        public int $shippingLocalAmount = 0,
        public int $shippingTotal = 0,
        public int $grandTotal = 0,
        public array $items = [],
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'promo_discount' => $this->promoDiscount,
            'new_user_discount' => $this->newUserDiscount,
            'voucher_discount' => $this->voucherDiscount,
            'total_discount' => $this->promoDiscount + $this->newUserDiscount + $this->voucherDiscount,
            'handling_fee' => $this->handlingFee,
            'product_total' => $this->productTotal,
            'shipping_jastip_amount' => $this->shippingJastipAmount,
            'shipping_local_amount' => $this->shippingLocalAmount,
            'shipping_total' => $this->shippingTotal,
            'grand_total' => $this->grandTotal,
            'items' => $this->items,
        ];
    }
}
