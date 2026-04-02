<?php

namespace App\Policies;

use App\Models\FamilyEvent;
use App\Models\User;

class FamilyEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FamilyEvent $familyEvent): bool
    {
        return $user->family_id === $familyEvent->family_id;
    }

    /**
     * Any family member can create calendar events.
     * Featured event creation (setting countdown) is parent-only
     * and enforced at the controller level.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Creator can edit their own events. Parents can edit any event.
     */
    public function update(User $user, FamilyEvent $familyEvent): bool
    {
        if ($user->family_id !== $familyEvent->family_id) {
            return false;
        }

        return $user->isParent() || $user->id === $familyEvent->created_by;
    }

    /**
     * Creator can delete their own events. Parents can delete any event.
     */
    public function delete(User $user, FamilyEvent $familyEvent): bool
    {
        if ($user->family_id !== $familyEvent->family_id) {
            return false;
        }

        return $user->isParent() || $user->id === $familyEvent->created_by;
    }
}
