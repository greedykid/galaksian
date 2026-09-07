<?php

namespace App\Models;

use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'trip_id',
        'shipment_id',
        'status',
        'currency',
        'exchange_rate',
        'address_snapshot',
        'product_subtotal',
        'product_discount_amount',
        'new_user_discount_amount',
        'voucher_id',
        'voucher_amount',
        'handling_fee_amount',
        'gift_fee_amount',
        'product_total',
        'shipping_jastip_amount',
        'shipping_local_amount',
        'shipping_total',
        'grand_total',
        'notes',
        'is_gift',
        'gift_from',
        'gift_to',
        'gift_message',
        'has_insurance',
        'product_paid_at',
        'shipping_paid_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'address_snapshot' => 'array',
            'product_subtotal' => 'integer',
            'product_discount_amount' => 'integer',
            'new_user_discount_amount' => 'integer',
            'voucher_amount' => 'integer',
            'handling_fee_amount' => 'integer',
            'gift_fee_amount' => 'integer',
            'product_total' => 'integer',
            'shipping_jastip_amount' => 'integer',
            'shipping_local_amount' => 'integer',
            'shipping_total' => 'integer',
            'grand_total' => 'integer',
            'exchange_rate' => 'integer',
            'is_gift' => 'boolean',
            'has_insurance' => 'boolean',
            'product_paid_at' => 'datetime',
            'shipping_paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function productInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->where('type', InvoiceType::PRODUCT);
    }

    public function shippingInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->where('type', InvoiceType::SHIPPING);
    }

    public function additionalInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->where('type', InvoiceType::ADDITIONAL);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function isPendingProductPayment(): bool
    {
        return $this->status === OrderStatus::PENDING_PAYMENT_PRODUCT;
    }

    public function isPaidProduct(): bool
    {
        return in_array($this->status, [
            OrderStatus::PAID_PRODUCT,
            OrderStatus::PROCESSING,
            OrderStatus::PACKING,
            OrderStatus::READY_FOR_DELIVERY,
            OrderStatus::PENDING_PAYMENT_SHIPPING,
            OrderStatus::SHIPPING_PAID,
            OrderStatus::DELIVERING,
            OrderStatus::COMPLETED,
        ], true);
    }

    public function canReorder(): bool
    {
        // Rule: TRX-P-09 Button order ulang ditiadakan selama status belum siap diantar
        return in_array($this->status, [
            OrderStatus::READY_FOR_DELIVERY,
            OrderStatus::PENDING_PAYMENT_SHIPPING,
            OrderStatus::SHIPPING_PAID,
            OrderStatus::DELIVERING,
            OrderStatus::COMPLETED,
        ], true);
    }
}
