<?php

namespace App\Enums;

enum RewardType: string
{
    case Standard = 'standard';
    case Auction = 'auction';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Auction => 'Auction',
        };
    }
}
