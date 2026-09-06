<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'reason' => $this->reason,
            'amount' => $this->amount,
            'status' => $this->status?->value,
            'refund_method' => $this->refund_method,
            'evidence_path' => $this->evidence_path,
            'approved_by' => $this->approved_by,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status?->value,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
