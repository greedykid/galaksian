<?php

namespace Tests\Feature\Api;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\ProductAvailability;
use App\Enums\TripStatus;
use App\Enums\UserRole;
use App\Enums\VoucherType;
use App\Models\Address;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('handling_fee_default', 5000);
        Setting::set('new_user_discount_enabled', '1');
        Setting::set('new_user_discount_amount', 10000);
    }

    public function test_guest_cannot_checkout(): void
    {
        $response = $this->postJson('/api/v1/checkout', [
            'address_id' => 1,
            'payment_method' => 'qris',
        ]);

        $response->assertStatus(401);
    }

    public function test_checkout_fails_without_active_trip(): void
    {
        $user = User::create([
            'name' => 'Checkout User',
            'phone' => '628100000001',
            'role' => UserRole::USER,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Checkout User',
            'phone' => '628100000001',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/checkout', [
                'address_id' => $address->id,
                'payment_method' => 'qris',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Tidak ada trip aktif untuk checkout',
            ]);
    }

    public function test_checkout_succeeds_with_active_trip_and_creates_invoice(): void
    {
        // Active Trip
        $trip = Trip::create([
            'code' => 'TRIP-ACT-01',
            'origin_country' => 'ID',
            'destination_country' => 'JP',
            'status' => TripStatus::ACTIVE,
        ]);

        $user = User::create([
            'name' => 'New Checkout User',
            'phone' => '628100000002',
            'role' => UserRole::USER,
            'is_new_user' => true,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'New Checkout User',
            'phone' => '628100000002',
            'address' => 'Jl. Kebon Jeruk No. 10',
        ]);

        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test']);
        $product = Product::create([
            'name' => 'Product Ready',
            'slug' => 'product-ready',
            'brand_id' => $brand->id,
            'price' => 50000,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'qty' => 1]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/checkout', [
                'address_id' => $address->id,
                'payment_method' => 'qris',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Checkout pesanan berhasil dilakukan.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'order' => ['id', 'order_number', 'status', 'items'],
                    'invoice' => ['id', 'invoice_number', 'type', 'status', 'amount'],
                    'payment' => ['payment_reference', 'instructions'],
                ],
            ]);

        // Verify order created
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT->value,
            'new_user_discount_amount' => 10000,
            'product_total' => 45000, // 50.000 - 10.000 (new user) + 5.000 (handling) = 45.000
        ]);

        // Verify invoice created
        $this->assertDatabaseHas('invoices', [
            'type' => InvoiceType::PRODUCT->value,
            'status' => InvoiceStatus::PENDING->value,
            'amount' => 45000,
        ]);

        // Verify user marked promo used
        $this->assertFalse((bool) $user->fresh()->is_new_user);

        // Verify cart is cleared
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_open_po_can_checkout_even_with_zero_stock(): void
    {
        Trip::create([
            'code' => 'TRIP-PO-01',
            'origin_country' => 'ID',
            'destination_country' => 'JP',
            'status' => TripStatus::ACTIVE,
        ]);

        $user = User::create([
            'name' => 'PO User',
            'phone' => '628100000003',
            'role' => UserRole::USER,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'PO User',
            'phone' => '628100000003',
            'address' => 'Jl. Thamrin No. 20',
        ]);

        $brand = Brand::create(['name' => 'Brand PO', 'slug' => 'brand-po']);
        $product = Product::create([
            'name' => 'Product PO',
            'slug' => 'product-po',
            'brand_id' => $brand->id,
            'price' => 75000,
            'stock' => 0, // Zero stock
            'availability_type' => ProductAvailability::OPEN_PO,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'qty' => 2]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/checkout', [
                'address_id' => $address->id,
                'payment_method' => 'qris',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_checkout_with_valid_voucher_succeeds(): void
    {
        $trip = Trip::create([
            'code' => 'TRIP-VOUCHER',
            'origin_country' => 'ID',
            'destination_country' => 'JP',
            'status' => TripStatus::ACTIVE,
        ]);

        $user = User::create([
            'name' => 'Voucher User',
            'phone' => '628100000004',
            'role' => UserRole::USER,
            'is_new_user' => false,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Voucher User',
            'phone' => '628100000004',
            'address' => 'Jl. Sudirman No. 5',
        ]);

        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test-v']);
        $product = Product::create([
            'name' => 'Product Test',
            'slug' => 'product-test-v',
            'brand_id' => $brand->id,
            'price' => 50000,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'status' => 'active']);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'qty' => 2]); // total 100k

        $voucher = Voucher::create([
            'code' => 'TESTVOUCHER10',
            'type' => VoucherType::PERCENT,
            'value' => 10,
            'max_discount' => 20000,
            'min_order_amount' => 50000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/checkout', [
                'address_id' => $address->id,
                'payment_method' => 'qris',
                'voucher_code' => 'TESTVOUCHER10',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order' => [
                        'pricing' => [
                            'voucher_amount' => 10000,
                            'product_total' => 95000,
                        ],
                    ],
                ],
            ]);
    }
}
