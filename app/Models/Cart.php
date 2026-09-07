<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_token',
        'voucher_id',
        'status',
        'currency',
        'is_gift',
        'gift_from',
        'gift_to',
        'gift_message',
    ];

    protected function casts(): array
    {
        return [
            'is_gift' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
