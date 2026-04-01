<?php

namespace App\Enums;

enum FamilyRole: string
{
    case Parent = 'parent';
    case Child = 'child';

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Parent => 'Parent',
            self::Child => 'Child',
        };
    }

    /**
     * Check if this role is a parent.
     */
    public function isParent(): bool
    {
        return $this === self::Parent;
    }

    /**
     * Check if this role is a child.
     */
    public function isChild(): bool
    {
        return $this === self::Child;
    }
}
