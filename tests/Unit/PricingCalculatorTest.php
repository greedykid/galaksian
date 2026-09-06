<?php

namespace Tests\Unit;

use App\Enums\ProductAvailability;
use App\Enums\UserRole;
use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Services\PricingCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected PricingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PricingCalculator;

        Setting::set('handling_fee_default', 5000);
        Setting::set('new_user_discount_enabled', '1');
        Setting::set('new_user_discount_amount', 10000);
    }

    public function test_it_calculates_subtotal_and_product_total_correctly(): void
    {
        $brand = Brand::create(['name' => 'Brand A', 'slug' => 'brand-a']);
        $product = Product::create([
            'name' => 'Product 1',
            'slug' => 'product-1',
            'brand_id' => $brand->id,
            'price' => 50000,
            'discount_price' => 40000, // discount 10k per item
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
        ]);

        $user = User::create([
            'name' => 'Existing User',
            'phone' => '628111111111',
            'role' => UserRole::USER,
            'is_new_user' => false,
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'qty' => 2]);

        $result = $this->calculator->calculateCart($cart, null, $user);

        // Subtotal = 50.000 * 2 = 100.000
        $this->assertEquals(100000, $result->subtotal);
        // Promo discount = (50.000 - 40.000) * 2 = 20.000
        $this->assertEquals(20000, $result->promoDiscount);
        // User is not new user -> 0
        $this->assertEquals(0, $result->newUserDiscount);
        // Voucher = 0
        $this->assertEquals(0, $result->voucherDiscount);
        // Handling fee = 5.000
        $this->assertEquals(5000, $result->handlingFee);
        // Product Total = 100.000 - 20.000 + 5.000 = 85.000
        $this->assertEquals(85000, $result->productTotal);
    }

    public function test_it_applies_new_user_discount_and_voucher(): void
    {
        $brand = Brand::create(['name' => 'Brand B', 'slug' => 'brand-b']);
        $product = Product::create([
            'name' => 'Product 2',
            'slug' => 'product-2',
            'brand_id' => $brand->id,
            'price' => 100000,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
        ]);

        $newUser = User::create([
            'name' => 'New User',
            'phone' => '628222222222',
            'role' => UserRole::USER,
            'is_new_user' => true,
        ]);

        $voucher = Voucher::create([
            'code' => 'TEST10',
            'type' => VoucherType::PERCENT,
            'value' => 10,
            'max_discount' => 15000,
            'min_order_amount' => 50000,
            'applicable_scope' => VoucherScope::PRODUCT,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $newUser->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'qty' => 1]);

        $result = $this->calculator->calculateCart($cart, $voucher, $newUser);

        // Subtotal = 100.000
        $this->assertEquals(100000, $result->subtotal);
        // New user discount = 10.000
        $this->assertEquals(10000, $result->newUserDiscount);
        // Remaining for voucher = 100.000 - 10.000 = 90.000. 10% of 90.000 = 9.000
        $this->assertEquals(9000, $result->voucherDiscount);
        // Product total = 100.000 - (10.000 + 9.000) + 5.000 = 86.000
        $this->assertEquals(86000, $result->productTotal);
    }

    public function test_it_calculates_shipping_total_and_grand_total(): void
    {
        $user = User::create([
            'name' => 'Order User',
            'phone' => '628333333333',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $user->id,
            'address_snapshot' => ['recipient_name' => 'Test'],
            'product_subtotal' => 100000,
            'product_total' => 105000,
            'shipping_jastip_amount' => 50000,
            'shipping_local_amount' => 20000,
        ]);

        $shippingResult = $this->calculator->calculateShipping($order);

        $this->assertEquals(70000, $shippingResult->shippingTotal);
        $this->assertEquals(175000, $shippingResult->grandTotal);
    }
}
