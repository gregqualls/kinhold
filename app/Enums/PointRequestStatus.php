<?php

namespace App\Enums;

enum PointRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Denied = 'denied';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Denied => 'Denied',
        };
    }
}
