<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductAvailability;
use App\Events\InvoicePaid;
use App\Exceptions\BusinessException;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function createCharge(Invoice $invoice, string $method): array
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            throw new BusinessException('Invoice ini sudah dibayar.');
        }

        $paymentMethod = PaymentMethod::tryFrom($method) ?? PaymentMethod::QRIS;
        $gatewayRef = 'PAY-'.strtoupper(Str::random(12));

        $instructions = match ($paymentMethod) {
            PaymentMethod::VIRTUAL_ACCOUNT => [
                'type' => 'virtual_account',
                'bank' => 'BCA',
                'va_number' => '8801'.rand(10000000, 99999999),
                'expiry_time' => $invoice->expired_at?->toIso8601String(),
            ],
            PaymentMethod::QRIS => [
                'type' => 'qris',
                'qr_string' => '00020101021226540014ID.LINKAJA.WWW01189360091100223746655204581253033605802ID5911GALAKSIAN6007JAKARTA61051234062070703A016304',
                'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=GALAKSIAN_'.$invoice->invoice_number,
                'expiry_time' => $invoice->expired_at?->toIso8601String(),
            ],
            PaymentMethod::PAYPAL => [
                'type' => 'paypal',
                'checkout_url' => 'https://www.sandbox.paypal.com/checkoutnow?token=EC-'.Str::random(17),
                'expiry_time' => $invoice->expired_at?->toIso8601String(),
            ],
            PaymentMethod::MANUAL => [
                'type' => 'manual',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'PT Galaksian Jastip Nusantara',
                'expiry_time' => $invoice->expired_at?->toIso8601String(),
            ],
        };

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'method' => $paymentMethod,
            'status' => PaymentStatus::PENDING,
            'amount' => $invoice->amount,
            'gateway_reference' => $gatewayRef,
            'raw_payload' => $instructions,
        ]);

        $invoice->update([
            'payment_method' => $paymentMethod,
            'gateway_reference' => $gatewayRef,
        ]);

        return [
            'invoice_number' => $invoice->invoice_number,
            'amount' => $invoice->amount,
            'method' => $paymentMethod->value,
            'payment_reference' => $gatewayRef,
            'instructions' => $instructions,
            'expired_at' => $invoice->expired_at?->toIso8601String(),
        ];
    }

    protected function extractHeaderValue(array $headers, string $key): ?string
    {
        if (! isset($headers[$key])) {
            return null;
        }
        $value = $headers[$key];
        if (is_array($value)) {
            return $value[0] ?? null;
        }

        return $value;
    }

    protected function verifyWebhookSignature(array $payload, array $headers = []): void
    {
        $source = $payload['source'] ?? 'payment_gateway';
        $config = config('services.payment', []);
        $signingSecret = $config['signing_secret'] ?? null;
        $callbackToken = $config['callback_token'] ?? null;

        // Jika belum ada secret gateway terkonfigurasi (dev/simulasi), lewati verifikasi.
        if (empty($signingSecret) && empty($callbackToken)) {
            return;
        }

        $isValid = false;

        if ($source === 'xendit' && $callbackToken) {
            // Xendit memakai header x-callback-token
            $token = $this->extractHeaderValue($headers, 'x-callback-token');
            $isValid = is_string($token) && hash_equals($callbackToken, $token);
        } else {
            // Payload signature (dari field `signature`) atau header x-signature
            $provided = $this->extractHeaderValue($headers, 'x-signature') ?? $payload['signature'] ?? null;
            $expected = $this->computeSignature($payload, $signingSecret, $source);
            if ($provided && $expected) {
                $isValid = hash_equals($expected, (string) $provided);
            }
        }

        if (! $isValid) {
            throw new BusinessException('Signature webhook tidak valid.');
        }
    }

    protected function computeSignature(array $payload, ?string $secret, string $source): ?string
    {
        if (empty($secret)) {
            return null;
        }

        // Midtrans: sha512(order_id.transaction_status.gross_amount.server_key)
        if (str_contains($source, 'midtrans')) {
            $orderId = $payload['order_id'] ?? $payload['invoice_number'] ?? '';
            $status = $payload['transaction_status'] ?? $payload['status'] ?? '';
            $amount = $payload['gross_amount'] ?? $payload['amount'] ?? '';
            $signature = strtoupper($orderId).$status.$amount.$secret;

            return hash('sha512', $signature);
        }

        // Default: HMAC-SHA256 dari payload kanonik (tanpa field signature & event-derived)
        $canonical = $payload;
        unset($canonical['signature'], $canonical['event_id'], $canonical['source']);
        $json = json_encode($canonical, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash_hmac('sha256', $json, $secret);
    }

    public function handleWebhook(array $payload, array $headers = []): array
    {
        // Verifikasi signature webhook (aktif hanya jika secret gateway dikonfigurasi)
        $this->verifyWebhookSignature($payload, $headers);

        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;
        $eventType = $payload['event_type'] ?? $payload['transaction_status'] ?? 'payment_notification';
        $source = $payload['source'] ?? 'payment_gateway';
        $signature = $this->extractHeaderValue($headers, 'x-signature') ?? $payload['signature'] ?? null;

        // Idempotency check: jika event_id sudah pernah diproses, abaikan
        if ($eventId) {
            $existingEvent = WebhookEvent::where('event_id', $eventId)->first();
            if ($existingEvent && $existingEvent->status === 'processed') {
                return [
                    'status' => 'ignored',
                    'message' => 'Event already processed',
                ];
            }
        }

        $webhookEvent = WebhookEvent::create([
            'source' => $source,
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $payload,
            'signature' => $signature,
            'status' => 'received',
        ]);

        return DB::transaction(function () use ($payload, $webhookEvent) {
            $invoiceNumber = $payload['invoice_number'] ?? null;
            $gatewayRef = $payload['gateway_reference'] ?? $payload['order_id'] ?? null;

            $invoice = null;
            if ($invoiceNumber) {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
            }
            if (! $invoice && $gatewayRef) {
                $invoice = Invoice::where('gateway_reference', $gatewayRef)->first();
            }

            if (! $invoice) {
                $webhookEvent->update(['status' => 'failed']);
                throw new BusinessException('Invoice tidak ditemukan untuk webhook ini.');
            }

            $paymentStatusStr = strtolower($payload['status'] ?? $payload['transaction_status'] ?? 'paid');

            $payment = Payment::where('invoice_id', $invoice->id)->latest()->first();
            if (! $payment) {
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'method' => $invoice->payment_method ?? PaymentMethod::QRIS,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $invoice->amount,
                    'gateway_reference' => $gatewayRef,
                    'raw_payload' => $payload,
                ]);
            }

            if (in_array($paymentStatusStr, ['capture', 'settlement', 'paid', 'success'], true)) {
                // Update payment & invoice to paid
                $payment->update([
                    'status' => PaymentStatus::PAID,
                    'paid_at' => now(),
                    'raw_payload' => array_merge($payment->raw_payload ?? [], $payload),
                ]);

                $invoice->update([
                    'status' => InvoiceStatus::PAID,
                    'paid_at' => now(),
                ]);

                $order = $invoice->order;

                // Handle order transitions
                if ($invoice->type === InvoiceType::PRODUCT) {
                    $this->orderStatusService->transition(
                        $order,
                        OrderStatus::PAID_PRODUCT,
                        "Pembayaran invoice produk {$invoice->invoice_number} berhasil."
                    );

                    // Decrement stock for ready stock items on paid
                    foreach ($order->items as $item) {
                        if ($item->availability_type === ProductAvailability::READY_STOCK) {
                            $prod = Product::find($item->product_id);
                            if ($prod) {
                                $prod->decrement('stock', $item->qty);
                            }
                        }
                    }
                } elseif ($invoice->type === InvoiceType::SHIPPING) {
                    $this->orderStatusService->transition(
                        $order,
                        OrderStatus::SHIPPING_PAID,
                        "Pembayaran invoice ongkos kirim {$invoice->invoice_number} berhasil."
                    );
                }

                event(new InvoicePaid($invoice, $payment));
            } elseif (in_array($paymentStatusStr, ['deny', 'cancel', 'expire', 'failed'], true)) {
                $statusEnum = in_array($paymentStatusStr, ['expire'], true) ? PaymentStatus::EXPIRED : PaymentStatus::FAILED;
                $payment->update([
                    'status' => $statusEnum,
                    'failed_at' => now(),
                ]);
                $invoice->update([
                    'status' => $statusEnum === PaymentStatus::EXPIRED ? InvoiceStatus::EXPIRED : InvoiceStatus::FAILED,
                ]);
            }

            $webhookEvent->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return [
                'status' => 'success',
                'invoice_number' => $invoice->invoice_number,
                'invoice_status' => $invoice->status->value,
                'order_id' => $invoice->order_id,
            ];
        });
    }
}
