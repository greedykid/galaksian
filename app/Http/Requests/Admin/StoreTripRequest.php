<?php

namespace App\Http\Requests\Admin;

use App\Enums\TripStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:trips,code'],
            'origin_country' => ['required', 'string', 'in:ID,JP'],
            'destination_country' => ['required', 'string', 'in:ID,JP'],
            'departure_at' => ['nullable', 'date'],
            'arrival_at' => ['nullable', 'date'],
            'cutoff_at' => ['nullable', 'date'],
            'status' => ['nullable', new Enum(TripStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
