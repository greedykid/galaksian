<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'role' => $this->role?->value ?? $this->role,
            'language' => $this->language,
            'identity_number' => $this->when(
                $request->user()?->isSuperAdmin() || $request->user()?->is($this->resource),
                $this->identity_number
            ),
            'is_new_user' => (bool) $this->is_new_user,
            'new_user_promo_used_at' => $this->new_user_promo_used_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'must_change_password' => (bool) $this->must_change_password,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
