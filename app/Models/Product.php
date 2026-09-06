<?php

namespace App\Models;

use App\Enums\ProductAvailability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'slug',
        'name',
        'description',
        'brand_id',
        'category_id',
        'origin_country',
        'currency',
        'price',
        'discount_price',
        'stock',
        'low_stock_threshold',
        'availability_type',
        'is_active',
        'is_flash_sale',
        'flash_sale_start_at',
        'flash_sale_end_at',
        'weight_gram',
        'length_cm',
        'width_cm',
        'height_cm',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'discount_price' => 'integer',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'availability_type' => ProductAvailability::class,
            'is_active' => 'boolean',
            'is_flash_sale' => 'boolean',
            'flash_sale_start_at' => 'datetime',
            'flash_sale_end_at' => 'datetime',
            'weight_gram' => 'integer',
            'length_cm' => 'integer',
            'width_cm' => 'integer',
            'height_cm' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFlashSale(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where('is_flash_sale', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('flash_sale_start_at')->orWhere('flash_sale_start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('flash_sale_end_at')->orWhere('flash_sale_end_at', '>=', $now);
            });
    }

    public function isCurrentlyFlashSale(): bool
    {
        if (! $this->is_flash_sale) {
            return false;
        }

        $now = now();
        if ($this->flash_sale_start_at && $this->flash_sale_start_at->isFuture()) {
            return false;
        }
        if ($this->flash_sale_end_at && $this->flash_sale_end_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getFinalPriceAttribute(): int
    {
        if ($this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price) {
            return $this->discount_price;
        }

        return $this->price;
    }
}
