<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'has_photo' => filled($this->photo_path),
            'api_address' => $this->api_address,
            'delivery_note' => $this->delivery_note?->value,
            'is_default' => (bool) $this->is_default,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
