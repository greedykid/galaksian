<?php

namespace App\Enums;

enum InvoiceType: string
{
    case PRODUCT = 'product';
    case SHIPPING = 'shipping';
    case ADDITIONAL = 'additional';
}
