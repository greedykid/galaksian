<?php

namespace App\Models;

use App\Enums\ProductAvailability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'brand_name_snapshot',
        'sku_snapshot',
        'qty',
        'unit_price',
        'original_price',
        'discount_amount',
        'subtotal',
        'availability_type',
        'refund_status',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'integer',
            'original_price' => 'integer',
            'discount_amount' => 'integer',
            'subtotal' => 'integer',
            'availability_type' => ProductAvailability::class,
            'refund_amount' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
