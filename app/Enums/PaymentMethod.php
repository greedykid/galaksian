<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case VIRTUAL_ACCOUNT = 'virtual_account';
    case QRIS = 'qris';
    case PAYPAL = 'paypal';
    case MANUAL = 'manual';
}
