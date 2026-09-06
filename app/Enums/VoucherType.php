<?php

namespace App\Enums;

enum VoucherType: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';
}
