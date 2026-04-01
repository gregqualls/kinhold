<?php

namespace App\Services;

use App\Enums\BadgeTriggerType;
use App\Enums\PointTransactionType;
use App\Models\Badge;
use App\Models\PointTransaction;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BadgeService
{
    /**
     * The single source of truth for default badge definitions.
     */
    public static function getDefaultBadgeDefinitions(): array
    {
        return [
            // --- Tasks Completed progression ---
            ['name' => 'First Steps', 'description' => 'Complete your first task', 'icon' => 'rocket', 'color' => '#059669', 'trigger_type' => BadgeTriggerType::TasksCompleted, 'trigger_threshold' => 1, 'is_hidden' => false, 'sort_order' => 0],
            ['name' => 'Task Rookie', 'description' => 'Complete 10 tasks', 'icon' => 'target', 'color' => '#0284c7', 'trigger_type' => BadgeTriggerType::TasksCompleted, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 1],
            ['name' => 'Task Machine', 'description' => 'Complete 50 tasks', 'icon' => 'lightning', 'color' => '#7d57a8', 'trigger_type' => BadgeTriggerType::TasksCompleted, 'trigger_threshold' => 50, 'is_hidden' => false, 'sort_order' => 2],
            ['name' => 'Task Legend', 'description' => 'Complete 100 tasks', 'icon' => 'crown', 'color' => '#d97706', 'trigger_type' => BadgeTriggerType::TasksCompleted, 'trigger_threshold' => 100, 'is_hidden' => true, 'sort_order' => 3],
            ['name' => 'Task Titan', 'description' => 'Complete 250 tasks', 'icon' => 'fist', 'color' => '#b91c1c', 'trigger_type' => BadgeTriggerType::TasksCompleted, 'trigger_threshold' => 250, 'is_hidden' => true, 'sort_order' => 4],

            // --- Task Streak progression ---
            ['name' => 'On Fire', 'description' => 'Complete tasks 7 days in a row', 'icon' => 'flame', 'color' => '#dc2626', 'trigger_type' => BadgeTriggerType::TaskStreak, 'trigger_threshold' => 7, 'is_hidden' => false, 'sort_order' => 5],
            ['name' => 'Unstoppable', 'description' => 'Complete tasks 30 days in a row', 'icon' => 'fire-ring', 'color' => '#e11d48', 'trigger_type' => BadgeTriggerType::TaskStreak, 'trigger_threshold' => 30, 'is_hidden' => true, 'sort_order' => 6],
            ['name' => 'Ironclad', 'description' => 'Complete tasks 60 days in a row', 'icon' => 'shield', 'color' => '#4338ca', 'trigger_type' => BadgeTriggerType::TaskStreak, 'trigger_threshold' => 60, 'is_hidden' => true, 'sort_order' => 7],

            // --- Points Earned progression ---
            ['name' => 'Rising Star', 'description' => 'Earn 100 points', 'icon' => 'star-burst', 'color' => '#0d9488', 'trigger_type' => BadgeTriggerType::PointsEarned, 'trigger_threshold' => 100, 'is_hidden' => false, 'sort_order' => 8],
            ['name' => 'Point Hunter', 'description' => 'Earn 500 points', 'icon' => 'gem', 'color' => '#7c49b6', 'trigger_type' => BadgeTriggerType::PointsEarned, 'trigger_threshold' => 500, 'is_hidden' => false, 'sort_order' => 9],
            ['name' => 'Point Lord', 'description' => 'Earn 1,000 points', 'icon' => 'trophy', 'color' => '#d97706', 'trigger_type' => BadgeTriggerType::PointsEarned, 'trigger_threshold' => 1000, 'is_hidden' => true, 'sort_order' => 10],
            ['name' => 'Diamond Collector', 'description' => 'Earn 5,000 points', 'icon' => 'diamond', 'color' => '#06b6d4', 'trigger_type' => BadgeTriggerType::PointsEarned, 'trigger_threshold' => 5000, 'is_hidden' => true, 'sort_order' => 11],

            // --- Kudos Given progression ---
            ['name' => 'Helping Hand', 'description' => 'Give 10 kudos to family members', 'icon' => 'heart-fire', 'color' => '#db2777', 'trigger_type' => BadgeTriggerType::KudosGiven, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 12],
            ['name' => 'Cheerleader', 'description' => 'Give 50 kudos to family members', 'icon' => 'thumbs-up', 'color' => '#ec4899', 'trigger_type' => BadgeTriggerType::KudosGiven, 'trigger_threshold' => 50, 'is_hidden' => false, 'sort_order' => 13],

            // --- Kudos Received progression ---
            ['name' => 'Fan Favorite', 'description' => 'Receive 10 kudos from family members', 'icon' => 'medal', 'color' => '#f59e0b', 'trigger_type' => BadgeTriggerType::KudosReceived, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 14],
            ['name' => 'Family MVP', 'description' => 'Receive 50 kudos from family members', 'icon' => 'crown', 'color' => '#a855f7', 'trigger_type' => BadgeTriggerType::KudosReceived, 'trigger_threshold' => 50, 'is_hidden' => true, 'sort_order' => 15],

            // --- Rewards Purchased progression ---
            ['name' => 'First Purchase', 'description' => 'Buy your first reward', 'icon' => 'gift', 'color' => '#10b981', 'trigger_type' => BadgeTriggerType::RewardsPurchased, 'trigger_threshold' => 1, 'is_hidden' => false, 'sort_order' => 16],
            ['name' => 'Reward Hunter', 'description' => 'Purchase 10 rewards', 'icon' => 'cake', 'color' => '#f97316', 'trigger_type' => BadgeTriggerType::RewardsPurchased, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 17],

            // --- Login Streak progression ---
            ['name' => 'Consistent', 'description' => 'Log in 7 days in a row', 'icon' => 'check-circle', 'color' => '#2563eb', 'trigger_type' => BadgeTriggerType::LoginStreak, 'trigger_threshold' => 7, 'is_hidden' => false, 'sort_order' => 18],
            ['name' => 'Dedicated', 'description' => 'Log in 30 days in a row', 'icon' => 'infinity', 'color' => '#7c3aed', 'trigger_type' => BadgeTriggerType::LoginStreak, 'trigger_threshold' => 30, 'is_hidden' => true, 'sort_order' => 19],

            // --- Easter Egg Discoveries (individual eggs — custom type, awarded directly) ---
            ['name' => 'Code Breaker', 'description' => 'Cracked the Konami Code', 'icon' => 'key', 'color' => '#059669', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 20],
            ['name' => 'Number Cruncher', 'description' => 'Why was 6 afraid of 7?', 'icon' => 'hashtag', 'color' => '#f59e0b', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 21],
            ['name' => 'Party Animal', 'description' => 'Started a legendary party', 'icon' => 'sun', 'color' => '#ec4899', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 22],
            ['name' => 'Mirror Mirror', 'description' => 'Saw everything backwards', 'icon' => 'eye', 'color' => '#06b6d4', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 23],
            ['name' => 'Red Pill', 'description' => 'Entered the digital rain', 'icon' => 'lightning', 'color' => '#22c55e', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 24],
            ['name' => 'Disco Inferno', 'description' => 'Got the groove going', 'icon' => 'music-note', 'color' => '#a855f7', 'trigger_type' => BadgeTriggerType::Custom, 'trigger_threshold' => null, 'is_hidden' => true, 'sort_order' => 25],

            // --- Master Explorer (auto-triggered when all 6 eggs found) ---
            ['name' => 'Master Explorer', 'description' => 'Found every single easter egg!', 'icon' => 'compass', 'color' => '#d97706', 'trigger_type' => BadgeTriggerType::EasterEgg, 'trigger_threshold' => 6, 'is_hidden' => true, 'sort_order' => 26],
        ];
    }

    /**
     * Create default badges for a family. Idempotent — uses firstOrCreate
     * so it's safe to call on new families AND existing ones with partial sets.
     *
     * @return Collection<Badge> The badges, keyed by name.
     */
    public static function createDefaultBadges(string $familyId, ?string $createdBy = null): Collection
    {
        $badges = collect();

        foreach (static::getDefaultBadgeDefinitions() as $data) {
            $badge = Badge::firstOrCreate(
                [
                    'family_id' => $familyId,
                    'name' => $data['name'],
                ],
                array_merge($data, [
                    'family_id' => $familyId,
                    'created_by' => $createdBy,
                    'trigger_type' => $data['trigger_type']->value,
                ])
            );
            $badges->put($data['name'], $badge);
        }

        return $badges;
    }

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
                    'id' => Str::uuid(),
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
            'id' => Str::uuid(),
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
            BadgeTriggerType::EasterEgg => $this->getEasterEggsFound($user),
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

            if (! $completed) {
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

    private function getEasterEggsFound(User $user): int
    {
        return count($user->easter_eggs_found ?? []);
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

            if (! $active) {
                break;
            }

            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }
}
