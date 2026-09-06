<?php

namespace App\Enums;

enum AddressDeliveryNote: string
{
    case LEAVE_AT_FRONT_DOOR = 'leave_at_front_door';
    case CONTACT_BEFORE_DELIVERY = 'contact_before_delivery';
    case HAND_TO_RECEIVER = 'hand_to_receiver';
    case SECURITY_DESK = 'security_desk';
    case OTHER = 'other';
}
