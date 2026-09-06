<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_number' => $this->shipment_number,
            'trip_id' => $this->trip_id,
            'status' => $this->status?->value,
            'origin_country' => $this->origin_country,
            'destination_country' => $this->destination_country,
            'bagasian_reference' => $this->bagasian_reference,
            'packing_estimate_weight' => $this->packing_estimate_weight,
            'packing_estimate_volume' => $this->packing_estimate_volume,
            'packing_estimate_cost' => $this->packing_estimate_cost,
            'sent_to_bagasian_at' => $this->sent_to_bagasian_at?->toIso8601String(),
            'bagasian_pdf_path' => $this->bagasian_pdf_path,
            'wa_message' => $this->wa_message,
            'notes' => $this->notes,
            'orders_count' => $this->orders()->count(),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            'trip' => $this->whenLoaded('trip', fn () => new TripResource($this->trip)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
