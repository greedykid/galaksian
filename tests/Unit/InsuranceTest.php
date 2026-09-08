<?php

namespace Tests\Feature\Api;

use App\Enums\ProductAvailability;
use App\Enums\TripStatus;
use App\Enums\UserRole;
use App\Http\Resources\OrderDetailResource;
use App\Models\Address;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsuranceTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(bool $hasInsurance, int $jastip = 50000, int $local = 10000): Order
    {
        $user = User::create([
            'name' => 'Insurance User',
            'phone' => '6281000000'.uniqid(),
            'role' => UserRole::USER,
        ]);

        return Order::create([
            'order_number' => 'ORD-TEST-'.uniqid(),
            'user_id' => $user->id,
            'status' => 'pending_payment_product',
            'currency' => 'IDR',
            'address_snapshot' => ['recipient_name' => 'Test', 'phone' => '62810', 'address' => 'Jl. Test'],
            'product_subtotal' => 100000,
            'product_total' => 100000,
            'shipping_jastip_amount' => $jastip,
            'shipping_local_amount' => $local,
            'shipping_total' => $jastip + $local,
            'grand_total' => 100000 + $jastip + $local,
            'has_insurance' => $hasInsurance,
            'insurance_amount' => $hasInsurance ? 2000 : 0,
        ]);
    }

    public function test_shipping_total_includes_insurance_when_enabled(): void
    {
        $order = $this->makeOrder(true);

        $calc = app(\App\Services\PricingCalculator::class);
        $pricing = $calc->calculateShipping($order);

        $this->assertEquals(2000, $pricing->insuranceAmount);
        $this->assertEquals(50000 + 10000 + 2000, $pricing->shippingTotal);
        $this->assertEquals(100000 + 50000 + 10000 + 2000, $pricing->grandTotal);
    }

    public function test_shipping_total_excludes_insurance_when_disabled(): void
    {
        $order = $this->makeOrder(false);

        $calc = app(\App\Services\PricingCalculator::class);
        $pricing = $calc->calculateShipping($order);

        $this->assertEquals(0, $pricing->insuranceAmount);
        $this->assertEquals(50000 + 10000, $pricing->shippingTotal);
    }

    public function test_order_detail_resource_exposes_insurance_in_pricing(): void
    {
        $order = $this->makeOrder(true);
        $resource = (new OrderDetailResource($order))->resolve();

        $this->assertEquals(2000, $resource['pricing']['insurance_amount']);
        $this->assertEquals(50000 + 10000 + 2000, $resource['pricing']['shipping_total']);
        $this->assertEquals(100000 + 50000 + 10000 + 2000, $resource['pricing']['grand_total']);
        $this->assertTrue($resource['has_insurance']);
    }
}
