<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'unique:users,phone,'.$userId],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', 'unique:users,email,'.$userId],
            'role' => ['sometimes', 'nullable', new Enum(UserRole::class)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            // Hanya SUPER_ADMIN yang boleh mengubah role user lain.
            if ($this->has('role') && ! $user?->isSuperAdmin()) {
                $validator->errors()->add('role', 'Hanya super admin yang dapat mengubah peran pengguna.');
            }

            $targetId = $this->route('id') ?? $this->route('user');
            $target = $targetId ? User::find($targetId) : null;
            if ($this->has('role') && $target && $target->id === $user?->id) {
                $validator->errors()->add('role', 'Super admin tidak dapat mengubah perannya sendiri.');
            }
            if ($this->has('role') && $target?->isSuperAdmin() && $this->input('role') !== UserRole::SUPER_ADMIN->value) {
                $validator->errors()->add('role', 'Role super admin tidak dapat diturunkan melalui endpoint ini.');
            }
        });
    }
}
