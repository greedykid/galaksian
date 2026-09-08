<?php

namespace App\Models;

use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'applicable_scope',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoucherType::class,
            'value' => 'integer',
            'max_discount' => 'integer',
            'min_order_amount' => 'integer',
            'usage_limit' => 'integer',
            'usage_per_user' => 'integer',
            'used_count' => 'integer',
            'applicable_scope' => VoucherScope::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function incrementUsedCount(): void
    {
        $this->increment('used_count');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Status kelayakan voucher untuk user tertentu.
     *
     * @return string salah satu: usable | inactive | not_started | expired |
     *                min_not_met | quota_exhausted | used_up
     */
    public function getState(?User $user, int $subtotal): string
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'not_started';
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'expired';
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return 'min_not_met';
        }

        if ($this->usage_limit !== null && (int) $this->used_count >= (int) $this->usage_limit) {
            return 'quota_exhausted';
        }

        if ($user && $this->usage_per_user !== null && $this->countUserUsage($user) >= $this->usage_per_user) {
            return 'used_up';
        }

        return 'usable';
    }

    public function countUserUsage(User $user): int
    {
        return Order::where('voucher_id', $this->id)
            ->where('user_id', $user->id)
            ->count();
    }

    public function getValidationError(?User $user, int $subtotal): ?string
    {
        return match ($this->getState($user, $subtotal)) {
            'inactive' => 'Voucher sudah tidak aktif.',
            'not_started' => 'Periode promo voucher belum dimulai.',
            'expired' => 'Voucher sudah kadaluarsa.',
            'min_not_met' => 'Minimal belanja untuk voucher ini adalah Rp '.number_format($this->min_order_amount, 0, ',', '.').'.',
            'quota_exhausted' => 'Kuota penggunaan voucher ini sudah habis.',
            'used_up' => 'Anda telah mencapai batas maksimal penggunaan voucher ini.',
            default => null,
        };
    }

    public function isValid(?User $user, int $subtotal): bool
    {
        return $this->getValidationError($user, $subtotal) === null;
    }

    public function calculateDiscount(int $subtotal): int
    {
        if ($this->type === VoucherType::FIXED) {
            return min($this->value, $subtotal);
        }

        // percent
        $discount = (int) round(($subtotal * $this->value) / 100);
        if ($this->max_discount !== null && $this->max_discount > 0) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $subtotal);
    }
}
