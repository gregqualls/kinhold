<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaultCategory;

class VaultCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VaultCategory $category): bool
    {
        return $user->family_id === $category->family_id;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, VaultCategory $category): bool
    {
        return $user->family_id === $category->family_id && $user->isParent();
    }

    public function delete(User $user, VaultCategory $category): bool
    {
        return $user->family_id === $category->family_id && $user->isParent();
    }
}
