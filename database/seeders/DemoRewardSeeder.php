<?php

namespace Database\Seeders;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\RewardPurchase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoRewardSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  REWARDS
        // ─────────────────────────────────────────────

        $sweets = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Sweets',
            'description' => 'Pick a treat from the candy stash',
            'point_cost' => 10,
            'icon' => 'cookie',
            'sort_order' => 0,
        ]);

        $screenTime = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Extra Screen Time (30 min)',
            'description' => 'Extra 30 minutes of screen time',
            'point_cost' => 20,
            'icon' => 'tv',
            'sort_order' => 1,
            'visibility' => 'child_only',
        ]);

        $pickDinner = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Pick Dinner',
            'description' => 'Choose what the family has for dinner',
            'point_cost' => 30,
            'icon' => 'pizza',
            'sort_order' => 2,
        ]);

        $moviePick = Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Movie Night Pick',
            'description' => 'Choose the family movie night film',
            'point_cost' => 40,
            'icon' => 'film',
            'sort_order' => 3,
            'quantity' => 2,
        ]);

        Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Stay Up Late',
            'description' => 'Stay up 1 hour past bedtime',
            'point_cost' => 75,
            'icon' => 'moon',
            'sort_order' => 4,
            'min_age' => 10,
            'visibility' => 'child_only',
        ]);

        Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Friend Sleepover',
            'description' => 'Invite a friend for a sleepover this weekend',
            'point_cost' => 100,
            'icon' => 'house',
            'sort_order' => 5,
            'quantity' => 1,
            'expires_at' => now()->addDays(30),
        ]);

        Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Skip Chore Day',
            'description' => 'Get a free pass on one day of chores',
            'point_cost' => 50,
            'icon' => 'confetti',
            'sort_order' => 6,
            'visibility' => 'specific',
            'visible_to' => [$this->emma->id, $this->jake->id],
        ]);

        // Auction rewards
        Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Weekend Trip Pick',
            'description' => 'Choose where the family goes this weekend! Highest bid wins.',
            'point_cost' => 0,
            'icon' => 'car',
            'sort_order' => 7,
            'reward_type' => 'auction',
            'quantity' => 1,
            'min_bid' => 20,
            'bid_end_at' => now()->addDays(5),
        ]);

        Reward::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Extra Allowance ($5)',
            'description' => 'Bid for a $5 bonus allowance. Parent calls the winner!',
            'point_cost' => 0,
            'icon' => 'dollar-sign',
            'sort_order' => 8,
            'reward_type' => 'auction',
            'quantity' => 1,
            'min_bid' => 10,
            'visibility' => 'child_only',
        ]);

        // ─────────────────────────────────────────────
        //  REWARD PURCHASES (with matching point transactions)
        // ─────────────────────────────────────────────

        $purchases = [
            ['user' => $this->emma, 'reward' => $sweets, 'days_ago' => 70],
            ['user' => $this->jake, 'reward' => $sweets, 'days_ago' => 65],
            ['user' => $this->emma, 'reward' => $screenTime, 'days_ago' => 50],
            ['user' => $this->lily, 'reward' => $sweets, 'days_ago' => 45],
            ['user' => $this->jake, 'reward' => $screenTime, 'days_ago' => 35],
            ['user' => $this->emma, 'reward' => $pickDinner, 'days_ago' => 25],
            ['user' => $this->lily, 'reward' => $sweets, 'days_ago' => 18],
            ['user' => $this->jake, 'reward' => $sweets, 'days_ago' => 12],
            ['user' => $this->emma, 'reward' => $moviePick, 'days_ago' => 7],
        ];

        foreach ($purchases as $p) {
            $purchasedAt = $now->copy()->subDays($p['days_ago'])->setHour(rand(15, 19));

            RewardPurchase::create([
                'family_id' => $this->familyId(),
                'reward_id' => $p['reward']->id,
                'user_id' => $p['user']->id,
                'points_spent' => $p['reward']->point_cost,
                'purchased_at' => $purchasedAt,
                'created_at' => $purchasedAt,
                'updated_at' => $purchasedAt,
            ]);

            PointTransaction::create([
                'family_id' => $this->familyId(),
                'user_id' => $p['user']->id,
                'type' => PointTransactionType::Redemption->value,
                'points' => -$p['reward']->point_cost,
                'description' => "Redeemed: {$p['reward']->title}",
                'source_type' => Reward::class,
                'source_id' => $p['reward']->id,
                'created_at' => $purchasedAt,
                'updated_at' => $purchasedAt,
            ]);
        }
    }
}
