<?php

namespace App\Models;

use App\Enums\AddressDeliveryNote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'photo_path',
        'api_address',
        'delivery_note',
        'is_default',
        'country',
        'province',
        'city',
        'district',
        'postal_code',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'delivery_note' => AddressDeliveryNote::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
