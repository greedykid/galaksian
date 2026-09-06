<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'origin_country',
        'destination_country',
        'departure_at',
        'arrival_at',
        'cutoff_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => TripStatus::class,
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
            'cutoff_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', TripStatus::ACTIVE)
            ->where(function ($q) {
                $q->whereNull('cutoff_at')->orWhere('cutoff_at', '>=', now());
            });
    }

    public function isOrderable(): bool
    {
        if ($this->status !== TripStatus::ACTIVE) {
            return false;
        }

        if ($this->cutoff_at && $this->cutoff_at->isPast()) {
            return false;
        }

        return true;
    }
}
