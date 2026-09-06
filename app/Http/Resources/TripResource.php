<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'origin_country' => $this->origin_country,
            'destination_country' => $this->destination_country,
            'departure_at' => $this->departure_at?->toIso8601String(),
            'arrival_at' => $this->arrival_at?->toIso8601String(),
            'cutoff_at' => $this->cutoff_at?->toIso8601String(),
            'status' => $this->status?->value,
            'is_orderable' => $this->isOrderable(),
            'notes' => $this->notes,
            'orders_count' => $this->whenCounted('orders'),
        ];
    }
}
