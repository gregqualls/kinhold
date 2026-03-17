<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;

class TaskListPolicy
{
    /**
     * Determine whether the user can view any task lists.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task list.
     *
     * @param User $user
     * @param TaskList $taskList
     * @return bool
     */
    public function view(User $user, TaskList $taskList): bool
    {
        return $user->currentFamily()->first()->id === $taskList->family_id;
    }

    /**
     * Determine whether the user can create task lists.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the task list.
     *
     * @param User $user
     * @param TaskList $taskList
     * @return bool
     */
    public function update(User $user, TaskList $taskList): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can delete the task list.
     *
     * @param User $user
     * @param TaskList $taskList
     * @return bool
     */
    public function delete(User $user, TaskList $taskList): bool
    {
        return $user->isParent();
    }
}
