<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ProductAvailability;
use App\Events\OrderCreated;
use App\Exceptions\BusinessException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected PricingCalculator $pricingCalculator,
        protected InvoiceService $invoiceService,
        protected PaymentGatewayService $paymentGatewayService
    ) {}

    public function checkout(User $user, array $payload): array
    {
        // 1. Validasi Trip Aktif
        $trip = Trip::active()->first();
        if (! $trip) {
            throw new BusinessException('Tidak ada trip aktif untuk checkout');
        }

        // 2. Validasi Alamat
        $address = Address::where('id', $payload['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $address) {
            throw new BusinessException('Alamat pengiriman tidak valid atau bukan milik Anda.');
        }

        // 3. Ambil Cart User
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $cart || $cart->items()->count() === 0) {
            throw new BusinessException('Keranjang belanja Anda masih kosong.');
        }

        $voucher = null;
        if (! empty($payload['voucher_code'])) {
            $voucher = Voucher::where('code', $payload['voucher_code'])->first();
            if (! $voucher) {
                throw new BusinessException('Voucher tidak ditemukan.');
            }

            $pricingWithoutVoucher = $this->pricingCalculator->calculateCart($cart, null, $user);
            $subtotalForVoucher = $pricingWithoutVoucher->subtotal - $pricingWithoutVoucher->promoDiscount;

            $voucherError = $voucher->getValidationError($user, $subtotalForVoucher);
            if ($voucherError) {
                throw new BusinessException($voucherError);
            }
        }

        $paymentMethod = $payload['payment_method'] ?? 'qris';
        $notes = $payload['notes'] ?? null;
        $isGift = (bool) ($payload['is_gift'] ?? $cart->is_gift ?? false);
        $hasInsurance = (bool) ($payload['has_insurance'] ?? false);

        return DB::transaction(function () use ($user, $trip, $address, $cart, $voucher, $paymentMethod, $notes, $isGift, $hasInsurance, $payload) {
            // 4. Validasi Stok dengan Pessimistic Locking
            $cartItems = $cart->items()->with('product')->get();
            $productIds = $cartItems->pluck('product_id')->all();

            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                if (! $product || ! $product->is_active) {
                    throw new BusinessException("Produk '{$cartItem->product?->name}' sudah tidak tersedia.");
                }

                if ($product->availability_type === ProductAvailability::READY_STOCK) {
                    if ($product->stock < $cartItem->qty) {
                        throw new BusinessException("Stok produk '{$product->name}' tidak mencukupi (tersisa {$product->stock}).");
                    }
                }
            }

            // 5. Hitung Pricing
            $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

            // 6. Buat Order
            $orderNumber = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(6));

            $addressSnapshot = [
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'address' => $address->address,
                'photo_path' => $address->photo_path,
                'api_address' => $address->api_address,
                'delivery_note' => $address->delivery_note?->value,
                'country' => $address->country,
                'province' => $address->province,
                'city' => $address->city,
                'district' => $address->district,
                'postal_code' => $address->postal_code,
            ];

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'trip_id' => $trip->id,
                'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
                'currency' => 'IDR',
                'address_snapshot' => $addressSnapshot,
                'product_subtotal' => $pricing->subtotal,
                'product_discount_amount' => $pricing->promoDiscount,
                'new_user_discount_amount' => $pricing->newUserDiscount,
                'voucher_id' => $voucher?->id,
                'voucher_amount' => $pricing->voucherDiscount,
                'handling_fee_amount' => $pricing->handlingFee,
                'gift_fee_amount' => $pricing->giftFee,
                'product_total' => $pricing->productTotal,
                'notes' => $notes,
                'is_gift' => $isGift,
                'gift_from' => $payload['gift_from'] ?? $cart->gift_from ?? null,
                'gift_to' => $payload['gift_to'] ?? $cart->gift_to ?? null,
                'gift_message' => $payload['gift_message'] ?? $cart->gift_message ?? null,
                'has_insurance' => $hasInsurance,
                'insurance_amount' => $hasInsurance ? (int) Setting::get('shipping_insurance_amount', 2000) : 0,
            ]);

            // Reservasi kuota voucher secara atomik (mencegah race condition)
            if ($voucher) {
                $lockedVoucher = Voucher::where('id', $voucher->id)->lockForUpdate()->first();
                if ($lockedVoucher) {
                    $lockedVoucher->incrementUsedCount();
                }
            }

            // 7. Buat Order Items Snapshot
            foreach ($pricing->items as $itemData) {
                $product = $products->get($itemData['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'brand_name_snapshot' => $product->brand?->name,
                    'sku_snapshot' => $product->sku,
                    'qty' => $itemData['qty'],
                    'unit_price' => $itemData['unit_price'],
                    'original_price' => $itemData['original_price'],
                    'discount_amount' => $itemData['discount_amount'],
                    'subtotal' => $itemData['subtotal'],
                    'availability_type' => $product->availability_type,
                ]);
            }

            // 8. Tandai Promo User Baru telah digunakan
            if ($pricing->newUserDiscount > 0) {
                $user->update([
                    'is_new_user' => false,
                    'new_user_promo_used_at' => now(),
                ]);
            }

            // 9. Catat Riwayat Status Order
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => OrderStatus::DRAFT->value,
                'to_status' => OrderStatus::PENDING_PAYMENT_PRODUCT->value,
                'note' => 'Order berhasil dibuat, menunggu pembayaran produk.',
                'actor_type' => 'user',
                'actor_id' => $user->id,
            ]);

            // 10. Buat Invoice Produk & Payment Intent
            $invoice = $this->invoiceService->createProductInvoice($order, $paymentMethod);
            $paymentCharge = $this->paymentGatewayService->createCharge($invoice, $paymentMethod);

            // 11. Bersihkan Cart
            $cart->items()->delete();

            event(new OrderCreated($order));

            return [
                'order' => $order->load(['items', 'trip', 'user']),
                'invoice' => $invoice,
                'payment' => $paymentCharge,
            ];
        });
    }
}
