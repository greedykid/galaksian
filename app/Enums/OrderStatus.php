<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case PENDING_PAYMENT_PRODUCT = 'pending_payment_product';
    case PAID_PRODUCT = 'paid_product';
    case PROCESSING = 'processing';
    case PACKING = 'packing';
    case READY_FOR_DELIVERY = 'ready_for_delivery';
    case PENDING_PAYMENT_SHIPPING = 'pending_payment_shipping';
    case SHIPPING_PAID = 'shipping_paid';
    case DELIVERING = 'delivering';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUND_REQUESTED = 'refund_requested';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_PAYMENT_PRODUCT => 'Menunggu Pembayaran Produk',
            self::PAID_PRODUCT => 'Produk Dibayar',
            self::PROCESSING => 'Diproses',
            self::PACKING => 'Packing',
            self::READY_FOR_DELIVERY => 'Siap Diantar',
            self::PENDING_PAYMENT_SHIPPING => 'Menunggu Pembayaran Ongkir',
            self::SHIPPING_PAID => 'Ongkir Dibayar',
            self::DELIVERING => 'Sedang Dikirim',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
            self::REFUND_REQUESTED => 'Pengajuan Refund',
            self::REFUNDED => 'Refund Selesai',
        };
    }
}
