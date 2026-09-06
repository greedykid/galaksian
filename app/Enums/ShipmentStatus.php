<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case DRAFT = 'draft';
    case ASSIGNED = 'assigned';
    case PACKING = 'packing';
    case SENT_TO_BAGASIAN = 'sent_to_bagasian';
    case READY_FOR_DELIVERY = 'ready_for_delivery';
    case DELIVERING = 'delivering';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
