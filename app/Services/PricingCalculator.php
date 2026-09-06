<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Services\DTO\PricingResult;

class PricingCalculator
{
    public function calculateCart(Cart $cart, ?Voucher $voucher = null, ?User $user = null): PricingResult
    {
        $items = $cart->items()->with('product')->get();

        $rawSubtotal = 0;
        $promoDiscount = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $originalPrice = (int) $product->price;
            $unitPrice = (int) $product->final_price;
            $itemDiscount = ($originalPrice - $unitPrice) * $item->qty;
            $itemSubtotal = $unitPrice * $item->qty;

            $rawSubtotal += ($originalPrice * $item->qty);
            $promoDiscount += $itemDiscount;

            $itemsData[] = [
                'cart_item_id' => $item->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'qty' => (int) $item->qty,
                'original_price' => $originalPrice,
                'unit_price' => $unitPrice,
                'discount_amount' => $itemDiscount,
                'subtotal' => $itemSubtotal,
                'availability_type' => $product->availability_type->value,
            ];
        }

        $subtotalAfterPromo = max(0, $rawSubtotal - $promoDiscount);

        // Diskon Pengguna Baru
        $newUserDiscount = 0;
        if ($user && $user->is_new_user) {
            $enabled = Setting::get('new_user_discount_enabled', '1');
            if ($enabled === '1' || $enabled === 'true' || $enabled === true) {
                $fixedAmount = (int) Setting::get('new_user_discount_amount', 0);
                $percent = (int) Setting::get('new_user_discount_percent', 0);

                if ($fixedAmount > 0) {
                    $newUserDiscount = min($fixedAmount, $subtotalAfterPromo);
                } elseif ($percent > 0) {
                    $newUserDiscount = (int) round(($subtotalAfterPromo * $percent) / 100);
                }
            }
        }

        $remainingSubtotal = max(0, $subtotalAfterPromo - $newUserDiscount);

        // Voucher
        $voucherDiscount = 0;
        if ($voucher && $voucher->isValid($user, $subtotalAfterPromo)) {
            $voucherDiscount = min($voucher->calculateDiscount($remainingSubtotal), $remainingSubtotal);
        }

        // Handling Fee
        $defaultHandlingFee = (int) Setting::get('handling_fee_default', 0);
        $handlingFee = $defaultHandlingFee;

        // Product Total
        $totalDiscount = $promoDiscount + $newUserDiscount + $voucherDiscount;
        $productTotal = max(0, $rawSubtotal - $totalDiscount + $handlingFee);

        return new PricingResult(
            subtotal: $rawSubtotal,
            promoDiscount: $promoDiscount,
            newUserDiscount: $newUserDiscount,
            voucherDiscount: $voucherDiscount,
            handlingFee: $handlingFee,
            productTotal: $productTotal,
            shippingJastipAmount: 0,
            shippingLocalAmount: 0,
            shippingTotal: 0,
            grandTotal: $productTotal,
            items: $itemsData,
        );
    }

    public function calculateShipping(Order $order): PricingResult
    {
        $shippingJastip = (int) ($order->shipping_jastip_amount ?? 0);
        $shippingLocal = (int) ($order->shipping_local_amount ?? 0);
        $shippingTotal = $shippingJastip + $shippingLocal;
        $grandTotal = (int) $order->product_total + $shippingTotal;

        return new PricingResult(
            subtotal: (int) $order->product_subtotal,
            promoDiscount: (int) $order->product_discount_amount,
            newUserDiscount: (int) $order->new_user_discount_amount,
            voucherDiscount: (int) $order->voucher_amount,
            handlingFee: (int) $order->handling_fee_amount,
            productTotal: (int) $order->product_total,
            shippingJastipAmount: $shippingJastip,
            shippingLocalAmount: $shippingLocal,
            shippingTotal: $shippingTotal,
            grandTotal: $grandTotal,
            items: [],
        );
    }
}
