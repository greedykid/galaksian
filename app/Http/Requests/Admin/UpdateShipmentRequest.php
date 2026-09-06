<?php

namespace App\Http\Requests\Admin;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', new Enum(ShipmentStatus::class)],
            'bagasian_reference' => ['nullable', 'string', 'max:100'],
            'packing_estimate_weight' => ['nullable', 'string', 'max:50'],
            'packing_estimate_volume' => ['nullable', 'string', 'max:50'],
            'packing_estimate_cost' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
