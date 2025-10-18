<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING_PAYMENT = 'pending payment';
    case WAITING_CONFIRMATION = 'waiting confirmation';
    case CONFIRMED = 'confirmed';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
