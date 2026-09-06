<?php

namespace Tests\Unit;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\UserRole;
use App\Exceptions\BusinessException;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceService;
    }

    public function test_it_creates_product_and_shipping_invoices(): void
    {
        $user = User::create([
            'name' => 'User Test',
            'phone' => '628666666666',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-INV-01',
            'user_id' => $user->id,
            'address_snapshot' => ['recipient_name' => 'User'],
            'product_subtotal' => 100000,
            'product_total' => 105000,
            'shipping_total' => 45000,
        ]);

        $productInv = $this->service->createProductInvoice($order, 'qris');
        $this->assertEquals(InvoiceType::PRODUCT, $productInv->type);
        $this->assertEquals(105000, $productInv->amount);
        $this->assertEquals(InvoiceStatus::PENDING, $productInv->status);

        $shippingInv = $this->service->createShippingInvoice($order, 'qris');
        $this->assertEquals(InvoiceType::SHIPPING, $shippingInv->type);
        $this->assertEquals(45000, $shippingInv->amount);
    }

    public function test_paid_invoice_cannot_be_modified(): void
    {
        $user = User::create([
            'name' => 'User Test',
            'phone' => '628777777777',
            'role' => UserRole::USER,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-INV-02',
            'user_id' => $user->id,
            'address_snapshot' => ['recipient_name' => 'User'],
            'product_subtotal' => 100000,
            'product_total' => 100000,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-PAID',
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PAID,
            'amount' => 100000,
        ]);

        $this->expectException(BusinessException::class);
        $this->service->updateInvoiceAmount($invoice, 120000);
    }
}
