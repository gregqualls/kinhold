<?php

namespace App\Services;

use App\Enums\BadgeTriggerType;
use App\Enums\PointTransactionType;
use App\Models\Badge;
use App\Models\PointTransaction;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class BadgeService
{
    /**
     * Check all badges for a user and award any newly earned ones.
     * Returns an array of newly earned badges.
     */
    public function checkAndAwardBadges(User $user): array
    {
        $familyBadges = Badge::where('family_id', $user->family_id)
            ->where('is_active', true)
            ->where('trigger_type', '!=', BadgeTriggerType::Custom)
            ->whereNotNull('trigger_threshold')
            ->get();

        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();
        $newlyEarned = [];

        foreach ($familyBadges as $badge) {
            if (in_array($badge->id, $earnedBadgeIds)) {
                continue;
            }

            $currentValue = $this->getCurrentValueForTrigger($user, $badge->trigger_type);

            if ($currentValue >= $badge->trigger_threshold) {
                $user->badges()->attach($badge->id, [
                    'earned_at' => now(),
                    'awarded_by' => null,
                ]);
                $newlyEarned[] = $badge;
            }
        }

        return $newlyEarned;
    }

    /**
     * Manually award a badge to a user.
     */
    public function manuallyAward(Badge $badge, User $user, User $awardedBy): void
    {
        if ($user->badges()->where('badges.id', $badge->id)->exists()) {
            return;
        }

        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
            'awarded_by' => $awardedBy->id,
        ]);
    }

    /**
     * Revoke a badge from a user.
     */
    public function revokeBadge(Badge $badge, User $user): void
    {
        $user->badges()->detach($badge->id);
    }

    /**
     * Get the user's current progress toward a specific trigger type.
     */
    public function getCurrentValueForTrigger(User $user, BadgeTriggerType $triggerType): int
    {
        return match ($triggerType) {
            BadgeTriggerType::PointsEarned => $this->getLifetimePointsEarned($user),
            BadgeTriggerType::TasksCompleted => $this->getCompletedTaskCount($user),
            BadgeTriggerType::TaskStreak => $this->getTaskStreak($user),
            BadgeTriggerType::KudosReceived => $this->getKudosReceived($user),
            BadgeTriggerType::KudosGiven => $this->getKudosGiven($user),
            BadgeTriggerType::RewardsPurchased => $this->getRewardsPurchased($user),
            BadgeTriggerType::LoginStreak => $this->getLoginStreak($user),
            BadgeTriggerType::Custom => 0,
        };
    }

    private function getLifetimePointsEarned(User $user): int
    {
        return (int) $user->pointTransactions()
            ->where('points', '>', 0)
            ->sum('points');
    }

    private function getCompletedTaskCount(User $user): int
    {
        // Count tasks completed by this user (assigned_to or completed family tasks)
        return Task::where('family_id', $user->family_id)
            ->whereNotNull('completed_at')
            ->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->count();
    }

    private function getTaskStreak(User $user): int
    {
        // Count consecutive days (backwards from today) where the user completed at least 1 task
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            $completed = PointTransaction::where('user_id', $user->id)
                ->where('type', PointTransactionType::TaskCompletion)
                ->whereDate('created_at', $date)
                ->exists();

            if (!$completed) {
                break;
            }

            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }

    private function getKudosReceived(User $user): int
    {
        return $user->pointTransactions()
            ->where('type', PointTransactionType::Kudos)
            ->count();
    }

    private function getKudosGiven(User $user): int
    {
        return PointTransaction::where('awarded_by', $user->id)
            ->where('type', PointTransactionType::Kudos)
            ->count();
    }

    private function getRewardsPurchased(User $user): int
    {
        return $user->pointTransactions()
            ->where('type', PointTransactionType::Redemption)
            ->count();
    }

    private function getLoginStreak(User $user): int
    {
        // Simplified: count consecutive days with any transaction activity
        // A proper implementation would track login events separately
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            $active = PointTransaction::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->exists();

            if (!$active) {
                break;
            }

            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }
}
