<?php

namespace App\Http\Requests\User;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_gift' => ['nullable', 'boolean'],
            'gift_from' => ['nullable', 'string', 'max:100'],
            'gift_to' => ['nullable', 'string', 'max:100'],
            'gift_message' => ['nullable', 'string', 'max:500'],
            'has_insurance' => ['nullable', 'boolean'],
        ];
    }
}
