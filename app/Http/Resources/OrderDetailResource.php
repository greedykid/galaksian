<?php

namespace App\Http\Resources;

use App\Enums\InvoiceStatus;
use App\Enums\RefundStatus;
use App\Models\Setting;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $adminPhone = Setting::get('admin_whatsapp_number', '6281200000001');
        $waMessage = "Halo admin Galaksian, saya ingin menanyakan pesanan saya #{$this->order_number}.";
        $waService = app(WhatsappService::class);
        $waContactLink = $waService->createShareLink($adminPhone, $waMessage);

        $pendingInvoice = $this->invoices()->where('status', InvoiceStatus::PENDING)->latest()->first();

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'notes' => $this->notes,
            'currency' => $this->currency,
            'address' => $this->address_snapshot,
            'pricing' => [
                'product_subtotal' => $this->product_subtotal,
                'product_discount_amount' => $this->product_discount_amount,
                'new_user_discount_amount' => $this->new_user_discount_amount,
                'voucher_amount' => $this->voucher_amount,
                'handling_fee_amount' => $this->handling_fee_amount,
                'product_total' => $this->product_total,
                'shipping_jastip_amount' => $this->shipping_jastip_amount,
                'shipping_local_amount' => $this->shipping_local_amount,
                'shipping_total' => $this->shipping_total,
                'grand_total' => $this->grand_total ?: ($this->product_total + ($this->shipping_total ?? 0)),
            ],
            'items' => OrderItemResource::collection($this->items),
            'invoices' => InvoiceResource::collection($this->invoices),
            'refunds' => $this->refunds ? $this->refunds->map(fn ($r) => [
                'id' => $r->id,
                'amount' => $r->amount,
                'status' => $r->status?->value,
                'reason' => $r->reason,
                'refund_method' => $r->refund_method,
                'created_at' => $r->created_at?->toIso8601String(),
            ]) : [],
            'remaining_refund_total' => (int) $this->refunds()->where('status', RefundStatus::PENDING)->sum('amount'),
            'status_histories' => OrderStatusHistoryResource::collection($this->statusHistories),
            'trip' => $this->trip ? [
                'id' => $this->trip->id,
                'code' => $this->trip->code,
                'origin_country' => $this->trip->origin_country,
                'destination_country' => $this->trip->destination_country,
                'departure_at' => $this->trip->departure_at?->toIso8601String(),
                'arrival_at' => $this->trip->arrival_at?->toIso8601String(),
            ] : null,
            'shipment' => $this->shipment ? [
                'id' => $this->shipment->id,
                'shipment_number' => $this->shipment->shipment_number,
                'status' => $this->shipment->status?->value,
                'packing_estimate_weight' => $this->shipment->packing_estimate_weight,
            ] : null,
            'actions' => [
                'can_pay' => $pendingInvoice !== null,
                'pending_invoice_id' => $pendingInvoice?->id,
                'can_download_invoice' => $this->invoices()->exists(),
                'download_invoice_url' => $this->invoices()->first() ? url("/api/v1/orders/{$this->id}/invoices/".$this->invoices()->first()->id.'/download') : null,
                'whatsapp_contact_url' => $waContactLink,
                'can_reorder' => $this->canReorder(),
            ],
            'timestamps' => [
                'created_at' => $this->created_at?->toIso8601String(),
                'product_paid_at' => $this->product_paid_at?->toIso8601String(),
                'shipping_paid_at' => $this->shipping_paid_at?->toIso8601String(),
                'completed_at' => $this->completed_at?->toIso8601String(),
                'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            ],
        ];
    }
}
