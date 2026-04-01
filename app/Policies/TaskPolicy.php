<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->family_id === $task->family_id;
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->family_id !== $task->family_id) {
            return false;
        }

        return $user->id === $task->created_by
            || $user->id === $task->assigned_to
            || $user->isParent();
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->family_id !== $task->family_id) {
            return false;
        }

        return $user->id === $task->created_by || $user->isParent();
    }

    /**
     * Determine whether the user can mark the task complete.
     */
    public function complete(User $user, Task $task): bool
    {
        if ($user->family_id !== $task->family_id) {
            return false;
        }

        // Family tasks can be completed by any family member
        if ($task->is_family_task) {
            return true;
        }

        return $user->id === $task->assigned_to || $user->isParent();
    }
}
