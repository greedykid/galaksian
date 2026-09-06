<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_number',
        'trip_id',
        'status',
        'origin_country',
        'destination_country',
        'bagasian_reference',
        'packing_estimate_weight',
        'packing_estimate_volume',
        'packing_estimate_cost',
        'sent_to_bagasian_at',
        'bagasian_pdf_path',
        'wa_message',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'packing_estimate_cost' => 'integer',
            'sent_to_bagasian_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
