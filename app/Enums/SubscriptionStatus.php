<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Paid = 'Paid';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}