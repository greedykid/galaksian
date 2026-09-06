<?php

namespace App\Enums;

enum VoucherScope: string
{
    case PRODUCT = 'product';
    case SHIPPING = 'shipping';
    case ALL = 'all';
}
