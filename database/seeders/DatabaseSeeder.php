<?php

namespace Database\Seeders;

use App\Enums\BadgeTriggerType;
use App\Enums\FamilyRole;
use App\Enums\PointTransactionType;
use App\Models\Badge;
use App\Models\Family;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed vault categories for all families
        $this->call(VaultCategorySeeder::class);

        // Create a demo family with parent and child for development
        $family = Family::create([
            'name' => 'Demo Family',
            'slug' => 'demo-family',
            'settings' => [
                'theme' => 'light',
                'notifications_enabled' => true,
                'modules' => [
                    'calendar' => true,
                    'tasks' => true,
                    'vault' => true,
                    'chat' => true,
                    'points' => true,
                    'badges' => true,
                ],
                'leaderboard_period' => 'weekly',
            ],
        ]);

        // Create parent user
        $parent = User::create([
            'name' => 'Demo Parent',
            'email' => 'parent@demo.local',
            'password' => bcrypt('password'),
            'family_id' => $family->id,
            'family_role' => FamilyRole::Parent,
            'timezone' => 'UTC',
        ]);

        // Create child user
        $child = User::create([
            'name' => 'Demo Child',
            'email' => 'child@demo.local',
            'password' => bcrypt('password'),
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'date_of_birth' => now()->subYears(14)->toDateString(),
            'timezone' => 'UTC',
        ]);

        // Create tags (replacing task lists)
        $generalTag = Tag::create([
            'family_id' => $family->id,
            'name' => 'General',
            'color' => '#3B82F6',
            'sort_order' => 0,
        ]);

        $choresTag = Tag::create([
            'family_id' => $family->id,
            'name' => 'Chores',
            'color' => '#10B981',
            'sort_order' => 1,
        ]);

        $schoolTag = Tag::create([
            'family_id' => $family->id,
            'name' => 'School',
            'color' => '#F59E0B',
            'sort_order' => 2,
        ]);

        // --- Tasks with point values + recurring ---

        // Recurring family task: Take out trash every Tuesday
        $trashTask = Task::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'assigned_to' => null,
            'title' => 'Take out trash',
            'description' => 'Take trash cans to the curb',
            'priority' => 'medium',
            'is_family_task' => true,
            'points' => 10,
            'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=TU',
        ]);
        $trashTask->tags()->attach($choresTag->id);

        // Completed tasks (for demo points)
        $completedTask1 = Task::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'assigned_to' => $child->id,
            'title' => 'Clean kitchen',
            'priority' => 'high',
            'points' => 20,
            'completed_at' => now()->subDays(3),
        ]);
        $completedTask1->tags()->attach($choresTag->id);

        $completedTask2 = Task::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'assigned_to' => $child->id,
            'title' => 'Vacuum living room',
            'priority' => 'medium',
            'points' => 10,
            'completed_at' => now()->subDays(2),
        ]);
        $completedTask2->tags()->attach($choresTag->id);

        $completedTask3 = Task::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'assigned_to' => $child->id,
            'title' => 'Organize bookshelf',
            'priority' => 'low',
            'points' => 5,
            'completed_at' => now()->subDays(1),
        ]);
        $completedTask3->tags()->attach($generalTag->id);

        // Pending tasks
        $lawnTask = Task::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'assigned_to' => $child->id,
            'title' => 'Mow the lawn',
            'priority' => 'high',
            'points' => 25,
            'due_date' => now()->addDays(2)->toDateString(),
        ]);
        $lawnTask->tags()->attach($choresTag->id);

        // --- Point Transactions (demo child has 45 pts in bank) ---

        // Task completions: +20, +10, +5 = 35
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::TaskCompletion->value,
            'points' => 20,
            'description' => 'Completed: Clean kitchen',
            'source_type' => Task::class,
            'source_id' => $completedTask1->id,
            'created_at' => now()->subDays(3),
        ]);

        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::TaskCompletion->value,
            'points' => 10,
            'description' => 'Completed: Vacuum living room',
            'source_type' => Task::class,
            'source_id' => $completedTask2->id,
            'created_at' => now()->subDays(2),
        ]);

        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::TaskCompletion->value,
            'points' => 5,
            'description' => 'Completed: Organize bookshelf',
            'source_type' => Task::class,
            'source_id' => $completedTask3->id,
            'created_at' => now()->subDays(1),
        ]);

        // Kudos: +2 = 37
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::Kudos->value,
            'points' => 1,
            'description' => 'Kudos from Demo Parent: Great job on the kitchen!',
            'awarded_by' => $parent->id,
            'created_at' => now()->subDays(3),
        ]);

        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::Kudos->value,
            'points' => 1,
            'description' => 'Kudos from Demo Parent: Thanks for helping out!',
            'awarded_by' => $parent->id,
            'created_at' => now()->subDays(1),
        ]);

        // Parent also has some points
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $parent->id,
            'type' => PointTransactionType::TaskCompletion->value,
            'points' => 10,
            'description' => 'Completed: Fix leaky faucet',
            'created_at' => now()->subDays(4),
        ]);

        // --- Rewards ---

        Reward::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'title' => 'Sweets',
            'description' => 'Pick a treat from the candy stash',
            'point_cost' => 10,
            'icon' => '🍬',
            'sort_order' => 0,
        ]);

        Reward::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'title' => 'TV Time (30 min)',
            'description' => 'Extra 30 minutes of screen time',
            'point_cost' => 20,
            'icon' => '📺',
            'sort_order' => 1,
        ]);

        Reward::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'title' => 'Pick Dinner',
            'description' => 'Choose what the family has for dinner',
            'point_cost' => 30,
            'icon' => '🍕',
            'sort_order' => 2,
        ]);

        Reward::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'title' => 'Movie Pick',
            'description' => 'Choose the family movie night film',
            'point_cost' => 40,
            'icon' => '🎬',
            'sort_order' => 3,
        ]);

        Reward::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'title' => 'Stay Up Late',
            'description' => 'Stay up 1 hour past bedtime',
            'point_cost' => 75,
            'icon' => '🌙',
            'sort_order' => 4,
        ]);

        // Adjustment to reach 45 pts: tasks(35) + kudos(2) + adjustment(8) = 45
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $child->id,
            'type' => PointTransactionType::Adjustment->value,
            'points' => 8,
            'description' => 'Welcome bonus points',
            'awarded_by' => $parent->id,
            'created_at' => now()->subDays(5),
        ]);

        // --- Badges (10 preset per family) ---

        $badgeData = [
            ['name' => 'First Steps', 'description' => 'Complete your first task', 'icon' => 'rocket', 'color' => '#059669', 'trigger_type' => BadgeTriggerType::TasksCompleted->value, 'trigger_threshold' => 1, 'is_hidden' => false, 'sort_order' => 0],
            ['name' => 'Task Rookie', 'description' => 'Complete 10 tasks', 'icon' => 'target', 'color' => '#0284c7', 'trigger_type' => BadgeTriggerType::TasksCompleted->value, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 1],
            ['name' => 'Task Machine', 'description' => 'Complete 50 tasks', 'icon' => 'lightning', 'color' => '#7d57a8', 'trigger_type' => BadgeTriggerType::TasksCompleted->value, 'trigger_threshold' => 50, 'is_hidden' => false, 'sort_order' => 2],
            ['name' => 'Task Legend', 'description' => 'Complete 100 tasks', 'icon' => 'crown', 'color' => '#d97706', 'trigger_type' => BadgeTriggerType::TasksCompleted->value, 'trigger_threshold' => 100, 'is_hidden' => true, 'sort_order' => 3],
            ['name' => 'On Fire', 'description' => 'Complete tasks 7 days in a row', 'icon' => 'flame', 'color' => '#dc2626', 'trigger_type' => BadgeTriggerType::TaskStreak->value, 'trigger_threshold' => 7, 'is_hidden' => false, 'sort_order' => 4],
            ['name' => 'Unstoppable', 'description' => 'Complete tasks 30 days in a row', 'icon' => 'fire-ring', 'color' => '#e11d48', 'trigger_type' => BadgeTriggerType::TaskStreak->value, 'trigger_threshold' => 30, 'is_hidden' => true, 'sort_order' => 5],
            ['name' => 'Rising Star', 'description' => 'Earn 100 points', 'icon' => 'star-burst', 'color' => '#0d9488', 'trigger_type' => BadgeTriggerType::PointsEarned->value, 'trigger_threshold' => 100, 'is_hidden' => false, 'sort_order' => 6],
            ['name' => 'Point Hunter', 'description' => 'Earn 500 points', 'icon' => 'gem', 'color' => '#7c49b6', 'trigger_type' => BadgeTriggerType::PointsEarned->value, 'trigger_threshold' => 500, 'is_hidden' => false, 'sort_order' => 7],
            ['name' => 'Point Lord', 'description' => 'Earn 1000 points', 'icon' => 'trophy', 'color' => '#d97706', 'trigger_type' => BadgeTriggerType::PointsEarned->value, 'trigger_threshold' => 1000, 'is_hidden' => true, 'sort_order' => 8],
            ['name' => 'Helping Hand', 'description' => 'Give 10 kudos to family members', 'icon' => 'heart-fire', 'color' => '#db2777', 'trigger_type' => BadgeTriggerType::KudosGiven->value, 'trigger_threshold' => 10, 'is_hidden' => false, 'sort_order' => 9],
        ];

        $badges = [];
        foreach ($badgeData as $data) {
            $badges[$data['name']] = Badge::create(array_merge($data, [
                'family_id' => $family->id,
                'created_by' => $parent->id,
                'is_active' => true,
            ]));
        }

        // Demo child has earned 2 badges
        $badges['First Steps']->users()->attach($child->id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'earned_at' => now()->subDays(3),
        ]);

        $welcomeBadge = Badge::create([
            'family_id' => $family->id,
            'created_by' => $parent->id,
            'name' => 'Welcome to Q32!',
            'description' => 'Joined the family hub',
            'icon' => 'shield',
            'color' => '#7d57a8',
            'trigger_type' => BadgeTriggerType::Custom->value,
            'trigger_threshold' => null,
            'is_hidden' => false,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $welcomeBadge->users()->attach($child->id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'earned_at' => now()->subDays(5),
            'awarded_by' => $parent->id,
        ]);
    }
}
