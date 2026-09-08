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
        public int $giftFee = 0,
        public int $productTotal = 0,
        public int $shippingJastipAmount = 0,
        public int $shippingLocalAmount = 0,
        public int $shippingTotal = 0,
        public int $insuranceAmount = 0,
        public int $grandTotal = 0,
        public array $items = [],
        public int $rawSubtotal = 0,
        public int $discountedSubtotal = 0,
    ) {}

    public function toArray(): array
    {
        $rawSub = $this->rawSubtotal > 0 ? $this->rawSubtotal : $this->subtotal;
        $discSub = $this->discountedSubtotal > 0 ? $this->discountedSubtotal : max(0, $rawSub - $this->promoDiscount);

        return [
            'subtotal' => $this->subtotal,
            'raw_subtotal' => $rawSub,
            'discounted_subtotal' => $discSub,
            'promo_discount' => $this->promoDiscount,
            'new_user_discount' => $this->newUserDiscount,
            'voucher_discount' => $this->voucherDiscount,
            'total_discount' => $this->promoDiscount + $this->newUserDiscount + $this->voucherDiscount,
            'handling_fee' => $this->handlingFee,
            'gift_fee' => $this->giftFee,
            'product_total' => $this->productTotal,
            'shipping_jastip_amount' => $this->shippingJastipAmount,
            'shipping_local_amount' => $this->shippingLocalAmount,
            'shipping_total' => $this->shippingTotal,
            'insurance_amount' => $this->insuranceAmount,
            'grand_total' => $this->grandTotal,
            'items' => $this->items,
        ];
    }
}
