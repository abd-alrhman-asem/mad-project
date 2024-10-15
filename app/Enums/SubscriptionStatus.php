<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Declined = 'declined';
}
