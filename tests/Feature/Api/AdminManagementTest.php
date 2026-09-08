<?php

namespace Tests\Feature\Api;

use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'phone' => '628199999999',
            'email' => 'admin@galaksian.test',
            'password' => Hash::make('secret123'),
            'role' => UserRole::ADMIN,
        ]);

        $this->adminToken = $this->admin->createToken('admin')->plainTextToken;
    }

    public function test_admin_login(): void
    {
        $response = $this->postJson('/api/v1/admin/auth/login', [
            'login' => 'admin@galaksian.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'user'],
            ]);
    }

    public function test_regular_user_is_blocked_from_admin_endpoints(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'phone' => '628100000005',
            'role' => UserRole::USER,
        ]);
        $userToken = $user->createToken('user')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$userToken}")
            ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_cs_can_view_orders_but_cannot_perform_finance_actions(): void
    {
        // CS boleh melihat data admin (read-only), mis. daftar order.
        $cs = User::create([
            'name' => 'CS Test',
            'phone' => '628111111111',
            'role' => UserRole::CS,
        ]);
        $csToken = $cs->createToken('cs')->plainTextToken;

        $listResponse = $this->withHeader('Authorization', "Bearer {$csToken}")
            ->getJson('/api/v1/admin/orders');
        $listResponse->assertStatus(200);

        // Tapi CS TIDAK boleh approve refund / buat invoice / ubah status order.
        $refundDeny = $this->withHeader('Authorization', "Bearer {$csToken}")
            ->getJson('/api/v1/admin/refunds');
        $refundDeny->assertStatus(200);

        $createInvoiceDeny = $this->withHeader('Authorization', "Bearer {$csToken}")
            ->postJson('/api/v1/admin/orders/1/invoices', []);
        $createInvoiceDeny->assertStatus(403);

        $statusDeny = $this->withHeader('Authorization', "Bearer {$csToken}")
            ->patchJson('/api/v1/admin/orders/1/status', ['status' => 'processing']);
        $statusDeny->assertStatus(403);
    }

    public function test_admin_dashboard_and_product_crud(): void
    {
        $dashResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->getJson('/api/v1/admin/dashboard');

        $dashResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['products', 'orders', 'revenue', 'shipments', 'users']]);

        $brand = Brand::create(['name' => 'Brand C', 'slug' => 'brand-c']);

        // Create product
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/products', [
                'name' => 'Product Baru Admin',
                'brand_id' => $brand->id,
                'price' => 75000,
                'stock' => 20,
                'availability_type' => 'ready_stock',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Product Baru Admin');

        $prodId = $createResponse->json('data.id');

        // Update product
        $updateResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->putJson("/api/v1/admin/products/{$prodId}", [
                'price' => 80000,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.price', 80000);

        // Delete (soft delete)
        $deleteResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/products/{$prodId}");

        $deleteResponse->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $prodId]);
    }

    public function test_admin_update_order_status_triggers_shipping_invoice(): void
    {
        $user = User::create([
            'name' => 'Order User',
            'phone' => '628100000006',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-ADM-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PAID_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Order User'],
            'product_subtotal' => 100000,
            'product_total' => 100000,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/orders/{$order->id}/status", [
                'status' => 'ready_for_delivery',
                'note' => 'Packing selesai, siap diantar',
                'shipping_jastip_amount' => 30000,
                'shipping_local_amount' => 15000,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'ready_for_delivery');

        $this->assertEquals(45000, $order->fresh()->shipping_total);

        // Verify shipping invoice automatically created
        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'type' => InvoiceType::SHIPPING->value,
            'amount' => 45000,
        ]);
    }

    public function test_admin_shipment_assign_and_send_bagasian(): void
    {
        $user = User::create([
            'name' => 'Order User 2',
            'phone' => '628100000007',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-ADM-02',
            'user_id' => $user->id,
            'status' => OrderStatus::PAID_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Order User 2'],
            'product_subtotal' => 100000,
            'product_total' => 100000,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHP-TEST-01',
            'origin_country' => 'ID',
            'destination_country' => 'JP',
            'status' => ShipmentStatus::DRAFT,
        ]);

        // Assign order to shipment
        $assignResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/v1/admin/orders/{$order->id}/assign-shipment", [
                'shipment_id' => $shipment->id,
            ]);

        $assignResponse->assertStatus(200);
        $this->assertEquals($shipment->id, $order->fresh()->shipment_id);

        // Send to Bagasian
        $bagasianResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/v1/admin/shipments/{$shipment->id}/send-bagasian");

        $bagasianResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['pdf_path', 'wa_link', 'status'],
            ]);

        $this->assertEquals(ShipmentStatus::SENT_TO_BAGASIAN, $shipment->fresh()->status);
    }

    public function test_admin_refund_flow(): void
    {
        $user = User::create([
            'name' => 'Refund User',
            'phone' => '628100000008',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-ADM-REFUND',
            'user_id' => $user->id,
            'status' => OrderStatus::PAID_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Refund User'],
            'product_subtotal' => 150000,
            'product_total' => 150000,
        ]);

        // Create refund
        $createRefundResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/refunds', [
                'order_id' => $order->id,
                'amount' => 150000,
                'reason' => 'Barang hilang saat pengiriman',
            ]);

        $createRefundResponse->assertStatus(201);
        $this->assertEquals(OrderStatus::REFUND_REQUESTED, $order->fresh()->status);

        $refundId = $createRefundResponse->json('data.id');

        // Approve refund
        $approveResponse = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/v1/admin/refunds/{$refundId}/approve");

        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // Order status becomes refunded, not hard deleted!
        $this->assertEquals(OrderStatus::REFUNDED, $order->fresh()->status);
        $this->assertNull($order->fresh()->deleted_at);
    }

    public function test_admin_login_response_includes_must_change_password_flag(): void
    {
        // Admin dengan must_change_password = true (mis. dibuat dengan password default)
        $this->admin->update(['must_change_password' => true]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'login' => 'admin@galaksian.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.must_change_password', true);
    }

    public function test_admin_change_password_clears_must_change_password_flag(): void
    {
        $this->admin->update(['must_change_password' => true]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/auth/change-password', [
                'current_password' => 'secret123',
                'password' => 'newsecret456',
                'password_confirmation' => 'newsecret456',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.must_change_password', false);

        $this->assertFalse($this->admin->fresh()->must_change_password);
        $this->assertTrue(Hash::check('newsecret456', $this->admin->fresh()->password));
    }
}
