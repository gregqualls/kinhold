<?php

namespace App\Policies;

use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use App\Models\User;

class ShoppingListPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id;
    }

    public function create(User $user): bool
    {
        return $user->isParent();
    }

    public function update(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function delete(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function completeTrip(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function clearChecked(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function moveItem(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id && $user->isParent();
    }

    public function toggleRecurring(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id && $user->isParent();
    }

    public function addItem(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function addRecipeToList(User $user, ShoppingList $list): bool
    {
        return $user->family_id === $list->family_id && $user->isParent();
    }

    public function updateItem(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id && $user->isParent();
    }

    public function removeItem(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id && $user->isParent();
    }

    public function checkItem(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id;
    }

    public function markOnHand(User $user, ShoppingItem $item): bool
    {
        return $user->family_id === $item->family_id;
    }

    public function manageStaples(User $user): bool
    {
        return $user->isParent();
    }
}
