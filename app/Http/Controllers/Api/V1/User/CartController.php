<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\ProductAvailability;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddToCartRequest;
use App\Http\Requests\User\ApplyVoucherRequest;
use App\Http\Requests\User\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\PricingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function __construct(
        protected PricingCalculator $pricingCalculator
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $voucher = $this->getVoucherForCart($request, $cart);
        $isGift = $this->getIsGiftForCart($request, $cart);

        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart))->withPricing($pricing, $voucher),
            'Keranjang berhasil diambil.'
        );
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $product = Product::active()->findOrFail($request->validated('product_id'));
        $qty = (int) $request->validated('qty');

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $newQty = $cartItem ? ($cartItem->qty + $qty) : $qty;

        if ($product->availability_type === ProductAvailability::READY_STOCK && $product->stock < $newQty) {
            throw new BusinessException("Stok tidak mencukupi. Tersedia: {$product->stock}");
        }

        if ($cartItem) {
            $cartItem->update(['qty' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $newQty,
            ]);
        }

        $voucher = $this->getVoucherForCart($request, $cart);
        $isGift = $this->getIsGiftForCart($request, $cart);
        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, $voucher),
            'Item berhasil ditambahkan ke keranjang.'
        );
    }

    public function updateItem(int $id, UpdateCartItemRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $id)->firstOrFail();
        $product = $cartItem->product;
        $qty = (int) $request->validated('qty');

        if ($product->availability_type === ProductAvailability::READY_STOCK && $product->stock < $qty) {
            throw new BusinessException("Stok tidak mencukupi. Tersedia: {$product->stock}");
        }

        $cartItem->update(['qty' => $qty]);

        $voucher = $this->getVoucherForCart($request, $cart);
        $isGift = $this->getIsGiftForCart($request, $cart);
        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, $voucher),
            'Jumlah item berhasil diperbarui.'
        );
    }

    public function removeItem(int $id, Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $id)->firstOrFail();
        $cartItem->delete();

        $voucher = $this->getVoucherForCart($request, $cart);
        $isGift = $this->getIsGiftForCart($request, $cart);
        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, $voucher),
            'Item berhasil dihapus dari keranjang.'
        );
    }

    public function applyVoucher(ApplyVoucherRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $user = $request->user('sanctum');
        $voucher = Voucher::where('code', $request->validated('code'))->firstOrFail();
        $isGift = $this->getIsGiftForCart($request, $cart);

        $pricingWithoutVoucher = $this->pricingCalculator->calculateCart($cart, null, $user, $isGift);
        $subtotal = $pricingWithoutVoucher->subtotal - $pricingWithoutVoucher->promoDiscount;

        $voucherError = $voucher->getValidationError($user, $subtotal);
        if ($voucherError) {
            throw new BusinessException($voucherError);
        }

        $cart->update(['voucher_id' => $voucher->id]);
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, $voucher),
            'Voucher berhasil digunakan.'
        );
    }

    public function removeVoucher(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->update(['voucher_id' => null]);
        $isGift = $this->getIsGiftForCart($request, $cart);
        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, null, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, null),
            'Voucher berhasil dihapus.'
        );
    }

    public function updateGiftOption(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $isGift = $request->boolean('is_gift');

        $cart->update([
            'is_gift' => $isGift,
            'gift_from' => $request->input('gift_from'),
            'gift_to' => $request->input('gift_to'),
            'gift_message' => $request->input('gift_message'),
        ]);

        $voucher = $this->getVoucherForCart($request, $cart);
        $user = $request->user('sanctum');
        $pricing = $this->pricingCalculator->calculateCart($cart, $voucher, $user, $isGift);

        return $this->successResponse(
            (new CartResource($cart->fresh()))->withPricing($pricing, $voucher),
            $isGift ? 'Layanan bingkisan diaktifkan.' : 'Layanan bingkisan dinonaktifkan.'
        );
    }

    protected function getVoucherForCart(Request $request, Cart $cart): ?Voucher
    {
        $voucherCode = $request->input('voucher_code') ?? $request->query('voucher_code');
        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->first();
            if ($voucher) {
                if ($cart->voucher_id !== $voucher->id) {
                    $cart->update(['voucher_id' => $voucher->id]);
                }

                return $voucher;
            }
        }

        if ($cart->voucher_id) {
            return $cart->voucher;
        }

        return null;
    }

    protected function getIsGiftForCart(Request $request, Cart $cart): bool
    {
        if ($request->has('is_gift')) {
            $isGift = $request->boolean('is_gift');
            if ($cart->is_gift !== $isGift) {
                $cart->update(['is_gift' => $isGift]);
            }

            return $isGift;
        }

        return (bool) $cart->is_gift;
    }

    protected function getOrCreateCart(Request $request): Cart
    {
        $user = $request->user('sanctum');

        if ($user) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                ['currency' => 'IDR']
            );

            // Merge guest cart if cart_token provided
            $token = $request->header('X-Cart-Token') ?? $request->input('cart_token');
            if ($token) {
                $guestCart = Cart::where('cart_token', $token)->where('status', 'active')->first();
                if ($guestCart && $guestCart->id !== $cart->id) {
                    foreach ($guestCart->items as $gItem) {
                        $existing = CartItem::where('cart_id', $cart->id)->where('product_id', $gItem->product_id)->first();
                        if ($existing) {
                            $existing->increment('qty', $gItem->qty);
                        } else {
                            $gItem->update(['cart_id' => $cart->id]);
                        }
                    }
                    $guestCart->update(['status' => 'merged']);
                }
            }

            return $cart;
        }

        $token = $request->header('X-Cart-Token') ?? $request->input('cart_token') ?? (string) Str::uuid();

        return Cart::firstOrCreate(
            ['cart_token' => $token, 'status' => 'active'],
            ['currency' => 'IDR']
        );
    }
}
