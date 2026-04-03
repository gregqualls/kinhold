<?php

namespace App\Policies;

use App\Models\Reward;
use App\Models\User;

class RewardPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, Reward $reward): bool
    {
        return $user->isParent();
    }

    public function delete(User $user, Reward $reward): bool
    {
        return $user->isParent();
    }

    public function purchase(User $user, Reward $reward): bool
    {
        return $reward->isVisibleTo($user) && $reward->isPurchasable();
    }
}
