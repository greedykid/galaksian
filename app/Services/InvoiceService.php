<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Exceptions\BusinessException;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;

class InvoiceService
{
    public function createProductInvoice(Order $order, string $paymentMethod = 'qris'): Invoice
    {
        $existing = $order->invoices()->where('type', InvoiceType::PRODUCT)->first();
        if ($existing) {
            return $existing;
        }

        $expiryMinutes = (int) Setting::get('invoice_expiry_minutes', 1440);

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber('PRD'),
            'order_id' => $order->id,
            'type' => InvoiceType::PRODUCT,
            'status' => InvoiceStatus::PENDING,
            'amount' => $order->product_total,
            'description' => "Tagihan Produk Pesanan #{$order->order_number}",
            'payment_method' => PaymentMethod::tryFrom($paymentMethod) ?? PaymentMethod::QRIS,
            'payment_gateway' => 'system_gateway',
            'expired_at' => now()->addMinutes($expiryMinutes),
            'created_by' => $order->user_id,
        ]);
    }

    public function createShippingInvoice(Order $order, string $paymentMethod = 'qris'): Invoice
    {
        $existing = $order->invoices()->where('type', InvoiceType::SHIPPING)->first();
        if ($existing) {
            return $existing;
        }

        $shippingTotal = (int) ($order->shipping_total ?? 0);
        if ($shippingTotal <= 0) {
            throw new BusinessException('Ongkir belum ditentukan oleh admin.');
        }

        $expiryMinutes = (int) Setting::get('invoice_expiry_minutes', 1440);

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber('SHP'),
            'order_id' => $order->id,
            'type' => InvoiceType::SHIPPING,
            'status' => InvoiceStatus::PENDING,
            'amount' => $shippingTotal,
            'description' => "Tagihan Ongkos Kirim Pesanan #{$order->order_number}",
            'payment_method' => PaymentMethod::tryFrom($paymentMethod) ?? PaymentMethod::QRIS,
            'payment_gateway' => 'system_gateway',
            'expired_at' => now()->addMinutes($expiryMinutes),
            'created_by' => null,
        ]);
    }

    public function createAdditionalInvoice(Order $order, array $data, ?User $creator = null): Invoice
    {
        $amount = (int) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw new BusinessException('Nominal invoice tambahan harus lebih dari 0.');
        }

        $expiryMinutes = (int) Setting::get('invoice_expiry_minutes', 1440);

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber('ADD'),
            'order_id' => $order->id,
            'type' => InvoiceType::ADDITIONAL,
            'status' => InvoiceStatus::PENDING,
            'amount' => $amount,
            'description' => $data['description'] ?? "Invoice Tambahan Pesanan #{$order->order_number}",
            'payment_method' => PaymentMethod::QRIS,
            'payment_gateway' => 'system_gateway',
            'expired_at' => now()->addMinutes($expiryMinutes),
            'created_by' => $creator?->id,
            'payload' => $data['payload'] ?? null,
        ]);
    }

    public function updateInvoiceAmount(Invoice $invoice, int $newAmount): Invoice
    {
        // Rule: Invoice paid tidak boleh diubah nominal
        if ($invoice->status === InvoiceStatus::PAID) {
            throw new BusinessException('Invoice yang sudah dibayar tidak boleh diubah nominalnya.');
        }

        $invoice->update(['amount' => $newAmount]);

        return $invoice;
    }

    protected function generateInvoiceNumber(string $prefix): string
    {
        return 'INV-'.$prefix.'-'.date('Ymd').'-'.strtoupper(Str::random(6));
    }
}
