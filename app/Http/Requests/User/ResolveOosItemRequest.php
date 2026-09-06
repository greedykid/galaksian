<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ResolveOosItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resolution' => 'required|in:refund,replace',
            'amount' => 'nullable|integer|min:1',
            'replacement_name' => 'required_if:resolution,replace|nullable|string|max:255',
            'replacement_price' => 'required_if:resolution,replace|nullable|integer|min:0',
        ];
    }
}
