<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'shipment_number' => ['nullable', 'string', 'max:50', 'unique:shipments,shipment_number'],
            'trip_id' => ['nullable', 'integer', 'exists:trips,id'],
            'origin_country' => ['nullable', 'string', 'in:ID,JP'],
            'destination_country' => ['nullable', 'string', 'in:ID,JP'],
            'bagasian_reference' => ['nullable', 'string', 'max:100'],
            'packing_estimate_weight' => ['nullable', 'string', 'max:50'],
            'packing_estimate_volume' => ['nullable', 'string', 'max:50'],
            'packing_estimate_cost' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
