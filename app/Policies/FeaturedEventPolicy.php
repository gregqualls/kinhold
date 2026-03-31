<?php

namespace App\Policies;

use App\Models\FeaturedEvent;
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

    public function update(User $user, FeaturedEvent $featuredEvent): bool
    {
        return $user->isParent();
    }

    public function delete(User $user, FeaturedEvent $featuredEvent): bool
    {
        return $user->isParent();
    }
}
