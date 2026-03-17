<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task.
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function view(User $user, Task $task): bool
    {
        return $user->currentFamily()->first()->id === $task->taskList->family_id;
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->created_by 
            || $user->id === $task->assigned_to 
            || $user->isParent();
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->created_by || $user->isParent();
    }

    /**
     * Determine whether the user can mark the task complete.
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function complete(User $user, Task $task): bool
    {
        // Family tasks can be completed by any family member
        if ($task->is_family_task && $user->family_id === $task->family_id) {
            return true;
        }

        return $user->id === $task->assigned_to || $user->isParent();
    }
}
