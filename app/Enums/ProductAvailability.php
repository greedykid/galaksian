<?php

namespace App\Enums;

enum ProductAvailability: string
{
    case READY_STOCK = 'ready_stock';
    case OPEN_PO = 'open_po';
}
