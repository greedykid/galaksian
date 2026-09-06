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
            'applicable_scope' => VoucherScope::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getValidationError(?User $user, int $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'Voucher sudah tidak aktif.';
        }

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Periode promo voucher belum dimulai.';
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'Voucher sudah kadaluarsa.';
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            $minFormatted = 'Rp '.number_format($this->min_order_amount, 0, ',', '.');

            return "Minimal belanja untuk voucher ini adalah {$minFormatted}.";
        }

        if ($this->usage_limit !== null) {
            $totalUsed = Order::where('voucher_id', $this->id)->count();
            if ($totalUsed >= $this->usage_limit) {
                return 'Kuota penggunaan voucher ini sudah habis.';
            }
        }

        if ($user && $this->usage_per_user !== null) {
            $userUsed = Order::where('voucher_id', $this->id)
                ->where('user_id', $user->id)
                ->count();
            if ($userUsed >= $this->usage_per_user) {
                return 'Anda telah mencapai batas maksimal penggunaan voucher ini.';
            }
        }

        return null;
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
