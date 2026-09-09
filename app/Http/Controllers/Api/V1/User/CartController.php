<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\ProductAvailability;
use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddToCartRequest;
use App\Http\Requests\User\ApplyVoucherRequest;
use App\Http\Requests\User\UpdateCartItemRequest;
use App\Http\Requests\User\UpdateGiftOptionRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
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

    public function availableVouchers(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $user = $request->user('sanctum');

        // Subtotal setelah promo, dipakai untuk menentukan kelayakan voucher (min_order, kuota, dll).
        $pricing = $this->pricingCalculator->calculateCart($cart, null, $user, (bool) $cart->is_gift);
        $subtotal = max(0, $pricing->subtotal - $pricing->promoDiscount);

        $vouchers = Voucher::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('value', 'desc')
            ->get();

        $data = $vouchers->map(function (Voucher $voucher) use ($user, $subtotal) {
            return $this->voucherToArray($voucher, $user, $subtotal);
        });

        return $this->successResponse($data, 'Daftar voucher berhasil diambil.');
    }

    protected function voucherToArray(Voucher $voucher, ?User $user = null, ?int $subtotal = null): array
    {
        $valueText = $voucher->type === VoucherType::FIXED
            ? 'Cashback Jastip '.$this->formatRupiah($voucher->value)
            : 'Diskon Jastip '.$voucher->value.'%';

        $minText = $voucher->min_order_amount > 0
            ? 'Min. belanja '.$this->formatRupiahCompact($voucher->min_order_amount)
            : 'Tanpa min. belanja';

        $scopeText = match ($voucher->applicable_scope) {
            VoucherScope::SHIPPING => 'Bebas Ongkir',
            default => 'Semua produk',
        };

        $icon = $voucher->type === VoucherType::FIXED ? 'tag' : 'percent';

        $state = $voucher->getState($user, $subtotal ?? 0);

        return [
            'code' => $voucher->code,
            'type' => $voucher->type->value,
            'value' => $voucher->value,
            'max_discount' => $voucher->max_discount,
            'min_order_amount' => $voucher->min_order_amount,
            'applicable_scope' => $voucher->applicable_scope?->value,
            'title' => $valueText,
            'description' => $minText.' · '.$scopeText,
            'icon' => $icon,
            'state' => $state,
            'ends_at' => $voucher->ends_at?->toISOString(),
        ];
    }

    protected function formatRupiah(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    protected function formatRupiahCompact(int $amount): string
    {
        if ($amount >= 1000000) {
            $val = $amount / 1000000;
            $txt = rtrim(rtrim(number_format($val, 1, ',', '.'), '0'), ',');

            return $txt.'jt';
        }
        if ($amount >= 1000) {
            $val = $amount / 1000;
            // 50 -> "50", 45 -> "45", 1000 -> "1000"
            $txt = (string) (int) $val;

            return $txt.'rb';
        }

        return (string) $amount;
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

    public function updateGiftOption(UpdateGiftOptionRequest $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $data = $request->validated();
        $isGift = (bool) $data['is_gift'];

        $cart->update([
            'is_gift' => $isGift,
            'gift_from' => $data['gift_from'] ?? null,
            'gift_to' => $data['gift_to'] ?? null,
            'gift_message' => $data['gift_message'] ?? null,
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

        $token = $request->header('X-Cart-Token') ?? (string) Str::uuid();

        return Cart::firstOrCreate(
            ['cart_token' => $token, 'status' => 'active'],
            ['currency' => 'IDR']
        );
    }
}
