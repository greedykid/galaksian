<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'avatar_url',
        'password',
        'identity_number',
        'role',
        'language',
        'is_new_user',
        'new_user_promo_used_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'identity_number' => 'encrypted',
            'role' => UserRole::class,
            'language' => 'string',
            'is_new_user' => 'boolean',
            'new_user_promo_used_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SUPER_ADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isCs(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SUPER_ADMIN, UserRole::CS], true);
    }
}
