<?php

namespace App\Policies;

use App\Models\FamilyEvent;
use App\Models\User;

class FeaturedEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, FamilyEvent $familyEvent): bool
    {
        return $user->isParent();
    }

    public function delete(User $user, FamilyEvent $familyEvent): bool
    {
        return $user->isParent();
    }
}
