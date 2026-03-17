<?php

namespace App\Enums;

enum BadgeTriggerType: string
{
    case PointsEarned = 'points_earned';
    case TasksCompleted = 'tasks_completed';
    case TaskStreak = 'task_streak';
    case KudosReceived = 'kudos_received';
    case KudosGiven = 'kudos_given';
    case RewardsPurchased = 'rewards_purchased';
    case LoginStreak = 'login_streak';
    case Custom = 'custom';

    public function label(): string
    {
        return match($this) {
            self::PointsEarned => 'Points Earned',
            self::TasksCompleted => 'Tasks Completed',
            self::TaskStreak => 'Task Streak',
            self::KudosReceived => 'Kudos Received',
            self::KudosGiven => 'Kudos Given',
            self::RewardsPurchased => 'Rewards Purchased',
            self::LoginStreak => 'Login Streak',
            self::Custom => 'Custom',
        };
    }
}
