<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', 'unique:users,email,'.$userId],
            'phone' => ['sometimes', 'nullable', 'string', 'max:25', 'unique:users,phone,'.$userId],
            'language' => ['sometimes', 'nullable', 'string', 'in:id,en'],
            'identity_number' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}
