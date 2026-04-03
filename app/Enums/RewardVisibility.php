<?php

namespace App\Enums;

enum RewardVisibility: string
{
    case Everyone = 'everyone';
    case ParentOnly = 'parent_only';
    case ChildOnly = 'child_only';
    case Specific = 'specific';

    public function label(): string
    {
        return match ($this) {
            self::Everyone => 'Everyone',
            self::ParentOnly => 'Parents Only',
            self::ChildOnly => 'Children Only',
            self::Specific => 'Specific People',
        };
    }
}
