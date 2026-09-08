<?php

namespace Tests\Feature\Api;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductAvailability;
use App\Enums\RefundStatus;
use App\Enums\TripStatus;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderOosTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Order $order;

    protected OrderItem $orderItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Tester User',
            'phone' => '081234567890',
        ]);

        $trip = Trip::create([
            'code' => 'TRIP-TEST-OOS',
            'origin_country' => 'ID',
            'destination_country' => 'JP',
            'status' => TripStatus::ACTIVE,
            'departure_at' => now()->addDays(2),
            'arrival_at' => now()->addDays(5),
        ]);

        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test']);
        $product = Product::create([
            'name' => 'Produk Awal Jepang',
            'slug' => 'produk-awal-jepang',
            'brand_id' => $brand->id,
            'price' => 100000,
            'discount_price' => null,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-TEST-OOS-01',
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'status' => OrderStatus::PAID_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Tester'],
            'product_subtotal' => 100000,
            'product_total' => 100000,
            'handling_fee_amount' => 0,
        ]);

        $this->orderItem = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => 'Produk Awal Jepang',
            'qty' => 1,
            'unit_price' => 100000,
            'original_price' => 100000,
            'subtotal' => 100000,
        ]);
    }

    public function test_user_cannot_manipulate_refund_amount(): void
    {
        // User mencoba mengirim amount yang lebih besar dari subtotal item (snapshot order).
        // Ini harus DITOLAK agar tidak bisa menggelembungkan refund / menandai invoice PAID.
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$this->orderItem->id}/resolve-oos", [
                'resolution' => 'refund',
                'amount' => 999999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        // Tidak ada refund yang dibuat & order item tidak berubah status refund.
        $this->assertDatabaseMissing('refunds', [
            'order_id' => $this->order->id,
        ]);
        $this->orderItem->refresh();
        $this->assertNull($this->orderItem->refund_status);
    }

    public function test_user_can_resolve_oos_with_cheaper_replacement_and_refund(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$this->orderItem->id}/resolve-oos", [
                'resolution' => 'replace',
                'replacement_name' => 'Produk Pengganti Murah',
                'replacement_price' => 60000,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->orderItem->refresh();
        $this->assertStringContainsString('Produk Pengganti Murah', $this->orderItem->product_name_snapshot);
        $this->assertEquals(60000, $this->orderItem->unit_price);
        $this->assertEquals(60000, $this->orderItem->subtotal);
        $this->assertEquals('requested', $this->orderItem->refund_status);
        $this->assertEquals(40000, $this->orderItem->refund_amount);

        // Verify refund record created
        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'amount' => 40000,
        ]);

        // Verify status history recorded
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $this->order->id,
        ]);
    }

    public function test_user_can_resolve_oos_with_expensive_replacement_and_additional_invoice(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$this->orderItem->id}/resolve-oos", [
                'resolution' => 'replace',
                'replacement_name' => 'Produk Pengganti Mahal',
                'replacement_price' => 150000,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->orderItem->refresh();
        $this->assertStringContainsString('Produk Pengganti Mahal', $this->orderItem->product_name_snapshot);
        $this->assertEquals(150000, $this->orderItem->unit_price);
        $this->assertEquals(150000, $this->orderItem->subtotal);

        // Verify additional invoice created
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'type' => InvoiceType::ADDITIONAL->value,
            'amount' => 50000,
        ]);
    }

    public function test_user_can_resolve_oos_with_partial_refund(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$this->orderItem->id}/resolve-oos", [
                'resolution' => 'refund',
                'amount' => 100000,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->orderItem->refresh();
        $this->assertEquals('requested', $this->orderItem->refund_status);
        $this->assertEquals(100000, $this->orderItem->refund_amount);

        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'amount' => 100000,
        ]);
    }

    public function test_scheme_b_user_orders_multiple_products_one_refunds_and_one_replaces_more_expensive_offsets_automatically(): void
    {
        // Setup item 1 (120k) and item 2 (100k)
        $item1 = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk 1 Habis',
            'qty' => 1,
            'unit_price' => 120000,
            'original_price' => 120000,
            'subtotal' => 120000,
        ]);

        $item2 = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk 2 Habis Mau Ganti',
            'qty' => 1,
            'unit_price' => 100000,
            'original_price' => 100000,
            'subtotal' => 100000,
        ]);

        // Step 1: User resolves Item 1 as Refund (Rp 120.000)
        $res1 = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$item1->id}/resolve-oos", [
                'resolution' => 'refund',
                'amount' => 120000,
            ]);
        $res1->assertStatus(200);
        $res1->assertJsonPath('data.remaining_refund_total', 120000);

        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'amount' => 120000,
            'status' => RefundStatus::PENDING->value,
        ]);

        // Step 2: User resolves Item 2 as Replace with more expensive item (Rp 150.000, diff +Rp 50.000)
        $res2 = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$item2->id}/resolve-oos", [
                'resolution' => 'replace',
                'replacement_name' => 'Produk Pengganti Lebih Mahal',
                'replacement_price' => 150000,
            ]);
        $res2->assertStatus(200);

        // SKEMA B VERIFICATION:
        // 1. Additional invoice was created and automatically marked PAID (LUNAS via offset)
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'type' => InvoiceType::ADDITIONAL->value,
            'amount' => 50000,
            'status' => InvoiceStatus::PAID->value,
        ]);

        // 2. Payment record created with status PAID and internal_refund_offset
        $this->assertDatabaseHas('payments', [
            'amount' => 50000,
            'status' => PaymentStatus::PAID->value,
        ]);

        // 3. Pending refund was reduced by 50.000 (120.000 - 50.000 = 70.000)
        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'amount' => 70000,
            'status' => RefundStatus::PENDING->value,
        ]);

        // 4. Response remaining_refund_total returns 70.000
        $res2->assertJsonPath('data.remaining_refund_total', 70000);

        // 5. Item 1 refund_amount reflects remaining 70.000
        $item1->refresh();
        $this->assertEquals(70000, $item1->refund_amount);
    }

    public function test_scheme_b_replace_more_expensive_first_then_refund_offsets_automatically(): void
    {
        $itemA = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk A',
            'qty' => 1,
            'unit_price' => 100000,
            'original_price' => 100000,
            'subtotal' => 100000,
        ]);

        $itemB = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk B',
            'qty' => 1,
            'unit_price' => 120000,
            'original_price' => 120000,
            'subtotal' => 120000,
        ]);

        // Step 1: User replaces Item A first with more expensive item (diff = +50.000)
        $resA = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$itemA->id}/resolve-oos", [
                'resolution' => 'replace',
                'replacement_name' => 'Produk A Pengganti',
                'replacement_price' => 150000,
            ]);
        $resA->assertStatus(200);

        // Additional invoice created unpaid
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'type' => InvoiceType::ADDITIONAL->value,
            'amount' => 50000,
            'status' => InvoiceStatus::PENDING->value,
        ]);

        // Step 2: User then resolves Item B as refund (120.000)
        $resB = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$itemB->id}/resolve-oos", [
                'resolution' => 'refund',
                'amount' => 120000,
            ]);
        $resB->assertStatus(200);

        // SKEMA B VERIFICATION:
        // 1. The previous unpaid additional invoice is now PAID!
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'type' => InvoiceType::ADDITIONAL->value,
            'amount' => 50000,
            'status' => InvoiceStatus::PAID->value,
        ]);

        // 2. Net refund created is 70.000 (120.000 - 50.000)
        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'amount' => 70000,
            'status' => RefundStatus::PENDING->value,
        ]);

        $resB->assertJsonPath('data.remaining_refund_total', 70000);
    }

    public function test_scheme_b_replacement_diff_exceeds_available_refund_creates_invoice_for_remaining_deficit(): void
    {
        $itemX = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk X',
            'qty' => 1,
            'unit_price' => 40000,
            'original_price' => 40000,
            'subtotal' => 40000,
        ]);

        $itemY = OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->orderItem->product_id,
            'product_name_snapshot' => 'Produk Y',
            'qty' => 1,
            'unit_price' => 100000,
            'original_price' => 100000,
            'subtotal' => 100000,
        ]);

        // Step 1: Refund item X (40.000)
        $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$itemX->id}/resolve-oos", [
                'resolution' => 'refund',
                'amount' => 40000,
            ])
            ->assertStatus(200);

        // Step 2: Replace item Y with diff +60.000 (price 160.000)
        $res = $this->actingAs($this->user)
            ->postJson("/api/v1/orders/{$this->order->id}/items/{$itemY->id}/resolve-oos", [
                'resolution' => 'replace',
                'replacement_name' => 'Produk Y Pengganti Mahal',
                'replacement_price' => 160000,
            ]);
        $res->assertStatus(200);

        // SKEMA B:
        // 1. Pending refund 40.000 was completely consumed
        $this->assertDatabaseHas('refunds', [
            'order_id' => $this->order->id,
            'status' => RefundStatus::COMPLETED->value,
            'amount' => 0,
        ]);

        // 2. Additional invoice created ONLY for deficit (60.000 - 40.000 = 20.000)
        $this->assertDatabaseHas('invoices', [
            'order_id' => $this->order->id,
            'type' => InvoiceType::ADDITIONAL->value,
            'amount' => 20000,
            'status' => InvoiceStatus::PENDING->value,
        ]);

        // 3. remaining_refund_total is 0
        $res->assertJsonPath('data.remaining_refund_total', 0);
    }
}
