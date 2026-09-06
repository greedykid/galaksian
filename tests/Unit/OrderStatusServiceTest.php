<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderStatusService;
    }

    public function test_legal_transitions_succeed_and_record_history(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '628444444444',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TRANS-01',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Budi'],
            'product_subtotal' => 50000,
            'product_total' => 55000,
        ]);

        $updated = $this->service->transition($order, OrderStatus::PAID_PRODUCT, 'Paid by QRIS', $user);

        $this->assertEquals(OrderStatus::PAID_PRODUCT, $updated->status);
        $this->assertNotNull($updated->product_paid_at);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => OrderStatus::PENDING_PAYMENT_PRODUCT->value,
            'to_status' => OrderStatus::PAID_PRODUCT->value,
            'note' => 'Paid by QRIS',
        ]);
    }

    public function test_illegal_transition_throws_exception(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '628555555555',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TRANS-02',
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING_PAYMENT_PRODUCT,
            'address_snapshot' => ['recipient_name' => 'Budi'],
            'product_subtotal' => 50000,
            'product_total' => 55000,
        ]);

        $this->expectException(BusinessException::class);
        $this->service->transition($order, OrderStatus::COMPLETED, 'Illegal jump to completed');
    }
}
