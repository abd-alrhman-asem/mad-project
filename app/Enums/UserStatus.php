<?php

namespace App\Enums;

enum UserStatus: string
{
    case Trial = 'Trial';
    case Expired = 'Expired';
}