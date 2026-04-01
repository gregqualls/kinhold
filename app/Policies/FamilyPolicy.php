<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;

class FamilyPolicy
{
    /**
     * Determine whether the user can update the family.
     */
    public function update(User $user, Family $family): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can invite members to the family.
     */
    public function invite(User $user, Family $family): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can add members to the family.
     */
    public function addMember(User $user, Family $family): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can update a family member.
     */
    public function updateMember(User $user, Family $family): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can remove a family member.
     */
    public function removeMember(User $user, Family $family): bool
    {
        return $user->isParent();
    }
}
