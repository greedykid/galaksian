<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'is_gift' => ['required', 'boolean'],
            'gift_from' => ['nullable', 'string', 'max:100'],
            'gift_to' => ['nullable', 'string', 'max:100'],
            'gift_message' => ['nullable', 'string', 'max:500'],
        ];
    }
}
