<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaultEntry;

class VaultEntryPolicy
{
    /**
     * Determine whether the user can view any vault entries.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the vault entry.
     */
    public function view(User $user, VaultEntry $entry): bool
    {
        // SECURITY: Must belong to the same family
        if ($user->family_id !== $entry->family_id) {
            return false;
        }

        if ($user->isParent()) {
            return true;
        }

        return $entry->permissions()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create vault entries.
     */
    public function create(User $user): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can update the vault entry.
     */
    public function update(User $user, VaultEntry $entry): bool
    {
        if ($user->family_id !== $entry->family_id) {
            return false;
        }

        if ($user->isParent()) {
            return true;
        }

        $permission = $entry->permissions()->where('user_id', $user->id)->first();

        return $permission && $permission->permission_level === 'edit';
    }

    /**
     * Determine whether the user can delete the vault entry.
     */
    public function delete(User $user, VaultEntry $entry): bool
    {
        return $user->family_id === $entry->family_id && $user->isParent();
    }

    /**
     * Determine whether the user can manage permissions.
     */
    public function managePermissions(User $user, VaultEntry $entry): bool
    {
        return $user->family_id === $entry->family_id && $user->isParent();
    }
}
