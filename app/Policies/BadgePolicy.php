<?php

namespace App\Policies;

use App\Models\Badge;
use App\Models\User;

class BadgePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, Badge $badge): bool
    {
        return $user->isParent();
    }

    public function delete(User $user, Badge $badge): bool
    {
        return $user->isParent();
    }

    public function award(User $user): bool
    {
        return $user->isParent();
    }

    public function revoke(User $user): bool
    {
        return $user->isParent();
    }
}
