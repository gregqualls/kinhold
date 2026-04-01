<?php

namespace App\Enums;

enum PermissionLevel: string
{
    case View = 'view';
    case Edit = 'edit';

    /**
     * Get a human-readable label for the permission level.
     */
    public function label(): string
    {
        return match ($this) {
            self::View => 'View Only',
            self::Edit => 'Edit',
        };
    }

    /**
     * Check if this permission allows editing.
     */
    public function canEdit(): bool
    {
        return $this === self::Edit;
    }

    /**
     * Check if this permission allows viewing.
     */
    public function canView(): bool
    {
        return true;
    }
}
