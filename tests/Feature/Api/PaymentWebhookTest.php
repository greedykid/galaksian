<?php

namespace Tests\Feature\Api;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductAvailability;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_webhook_updates_order_status_and_decrements_stock(): void
    {
        $user = User::create([
            'name' => 'Buyer',
            'phone' => '628100000004',
            'role' => UserRole::USER,
        ]);

        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test']);
        $product = Product::create([
            'name' => 'Item Test',
            'slug' => 'item-test',
            'brand_id' => $brand->id,
            'price' => 50000,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-WEBHOOK-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Buyer'],
            'product_subtotal' => 50000,
            'product_total' => 50000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'qty' => 2,
            'unit_price' => 50000,
            'original_price' => 50000,
            'subtotal' => 100000,
            'availability_type' => ProductAvailability::READY_STOCK,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-WEBHOOK-01',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => 50000,
            'payment_method' => PaymentMethod::QRIS,
            'gateway_reference' => 'REF-001',
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'method' => PaymentMethod::QRIS,
            'status' => PaymentStatus::PENDING,
            'amount' => 50000,
            'gateway_reference' => 'REF-001',
        ]);

        $payload = [
            'event_id' => 'evt_test_123',
            'invoice_number' => 'INV-WEBHOOK-01',
            'status' => 'settlement',
            'amount' => 50000,
        ];

        $response = $this->postJson('/api/v1/webhooks/payment', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'success',
                    'invoice_number' => 'INV-WEBHOOK-01',
                    'invoice_status' => 'paid',
                ],
            ]);

        // Verify order is paid_product
        $this->assertEquals(OrderStatus::PAID_PRODUCT, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->product_paid_at);

        // Verify stock decremented: 10 - 2 = 8
        $this->assertEquals(8, $product->fresh()->stock);

        // Verify history recorded
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => OrderStatus::PAID_PRODUCT->value,
        ]);

        // Idempotency: Send the exact same webhook event again
        $duplicateResponse = $this->postJson('/api/v1/webhooks/payment', $payload);
        $duplicateResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'ignored',
                    'message' => 'Event already processed',
                ],
            ]);

        // Stock should still be 8 (not decremented twice)
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_payment_webhook_rejects_invalid_signature(): void
    {
        config(['services.payment.signing_secret' => 'test_secret']);

        $user = User::create(['name' => 'Buyer', 'phone' => '628100000009', 'role' => UserRole::USER]);
        $order = Order::create([
            'order_number' => 'ORD-SIG-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Buyer'],
            'product_subtotal' => 50000,
            'product_total' => 50000,
        ]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-SIG-01',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => 50000,
            'payment_method' => PaymentMethod::QRIS,
        ]);

        $payload = [
            'event_id' => 'evt_sig_bad',
            'invoice_number' => 'INV-SIG-01',
            'status' => 'settlement',
            'amount' => 50000,
        ];

        // Signature tidak valid harus ditolak
        $response = $this->postJson('/api/v1/webhooks/payment', $payload, ['x-signature' => 'invalid_signature']);

        $response->assertStatus(422);

        // Invoice tetap pending (tidak di-mark paid)
        $this->assertEquals(InvoiceStatus::PENDING, $invoice->fresh()->status);
    }

    public function test_payment_webhook_accepts_valid_signature(): void
    {
        $secret = 'test_secret';
        config(['services.payment.signing_secret' => $secret]);

        $user = User::create(['name' => 'Buyer', 'phone' => '628100000008', 'role' => UserRole::USER]);
        $brand = Brand::create(['name' => 'Brand Test', 'slug' => 'brand-test']);
        $product = Product::create([
            'name' => 'Item Test',
            'slug' => 'item-test',
            'brand_id' => $brand->id,
            'price' => 50000,
            'stock' => 10,
            'availability_type' => ProductAvailability::READY_STOCK,
            'is_active' => true,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-SIG-02',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Buyer'],
            'product_subtotal' => 50000,
            'product_total' => 50000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'qty' => 1,
            'unit_price' => 50000,
            'original_price' => 50000,
            'subtotal' => 50000,
            'availability_type' => ProductAvailability::READY_STOCK,
        ]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-SIG-02',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => 50000,
            'payment_method' => PaymentMethod::QRIS,
        ]);

        $payload = [
            'event_id' => 'evt_sig_ok',
            'invoice_number' => 'INV-SIG-02',
            'status' => 'settlement',
            'amount' => 50000,
        ];
        // Signature = HMAC-SHA256 dari payload kanonik (tanpa signature/event_id/source)
        $canonical = $payload;
        unset($canonical['signature'], $canonical['event_id'], $canonical['source']);
        $sig = hash_hmac('sha256', json_encode($canonical, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $secret);

        $response = $this->postJson('/api/v1/webhooks/payment', $payload, ['x-signature' => $sig]);

        $response->assertStatus(200);
        $this->assertEquals(InvoiceStatus::PAID, $invoice->fresh()->status);
    }
}
