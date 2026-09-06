<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_id' => $this->order_id,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'amount' => $this->amount,
            'description' => $this->description,
            'payment_method' => $this->payment_method?->value,
            'payment_gateway' => $this->payment_gateway,
            'gateway_reference' => $this->gateway_reference,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'expired_at' => $this->expired_at?->toIso8601String(),
            'latest_payment' => $this->whenLoaded('latestPayment', fn () => new PaymentResource($this->latestPayment)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
