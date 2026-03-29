<?php

namespace Database\Seeders;

use App\Enums\BadgeTriggerType;
use App\Enums\FamilyRole;
use App\Enums\PointTransactionType;
use App\Models\Badge;
use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\FeaturedEvent;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\RewardPurchase;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Models\VaultCategory;
use App\Models\FamilyEvent;
use App\Models\VaultEntry;
use App\Services\BadgeService;
use App\Services\VaultEncryptionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with a rich 3-month demo.
     *
     * Family: The Johnsons — Mike & Sarah (parents), Emma (16), Jake (13), Lily (9)
     *
     * Login credentials:
     *   parent@demo.local / password  (Mike - parent)
     *   sarah@demo.local  / password  (Sarah - parent)
     *   emma@demo.local   / password  (Emma - child, 16)
     *   Jake & Lily are managed accounts (switch from parent Settings)
     */
    public function run(): void
    {
        // Idempotent: if demo family exists, wipe and re-seed so data stays fresh
        // Uses a reserved slug + @demo.local emails that real families can't claim.
        // Safe to run on every deploy — wipes and re-creates demo data only.
        // Also checks for legacy slug/invite_code from older seeds.
        $existing = Family::where('slug', 'q32-demo-family')
            ->orWhere('slug', 'johnsons')
            ->orWhere('invite_code', 'JOHNSON2026')
            ->first();
        if ($existing) {
            // Delete all related data (cascades handle most, but be thorough)
            User::where('family_id', $existing->id)->delete();
            Task::where('family_id', $existing->id)->delete();
            Tag::where('family_id', $existing->id)->delete();
            Reward::where('family_id', $existing->id)->delete();
            Badge::where('family_id', $existing->id)->delete();
            FeaturedEvent::where('family_id', $existing->id)->delete();
            FamilyEvent::where('family_id', $existing->id)->delete();
            VaultEntry::where('family_id', $existing->id)->delete();
            VaultCategory::where('family_id', $existing->id)->delete();
            PointTransaction::where('family_id', $existing->id)->delete();
            ChatMessage::where('family_id', $existing->id)->delete();
            $existing->delete();
            $this->command?->info('Cleared existing demo family data.');
        }

        $now = Carbon::now();
        $vault = new VaultEncryptionService();

        // ─────────────────────────────────────────────
        //  FAMILY
        // ─────────────────────────────────────────────

        $family = Family::create([
            'name' => 'The Johnsons',
            'slug' => 'q32-demo-family',
            'invite_code' => 'JOHNSON2026',
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
                'default_points_low' => 5,
                'default_points_medium' => 10,
                'default_points_high' => 20,
                'kudos_cost_enabled' => false,
                'ai_provider' => 'anthropic',
            ],
        ]);

        // ─────────────────────────────────────────────
        //  USERS
        // ─────────────────────────────────────────────

        $mike = User::create([
            'name' => 'Mike',
            'email' => 'parent@demo.local',
            'password' => bcrypt('password'),
            'family_id' => $family->id,
            'family_role' => FamilyRole::Parent,
            'date_of_birth' => '1984-06-15',
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
        ]);

        $sarah = User::create([
            'name' => 'Sarah',
            'email' => 'sarah@demo.local',
            'password' => bcrypt('password'),
            'family_id' => $family->id,
            'family_role' => FamilyRole::Parent,
            'date_of_birth' => '1986-03-22',
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
        ]);

        $emma = User::create([
            'name' => 'Emma',
            'email' => 'emma@demo.local',
            'password' => bcrypt('password'),
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'date_of_birth' => $now->copy()->subYears(16)->subMonths(3)->toDateString(),
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
        ]);

        $jake = User::create([
            'name' => 'Jake',
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'is_managed' => true,
            'managed_by' => $mike->id,
            'date_of_birth' => $now->copy()->subYears(13)->subMonths(7)->toDateString(),
            'timezone' => 'America/Chicago',
        ]);

        $lily = User::create([
            'name' => 'Lily',
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'is_managed' => true,
            'managed_by' => $sarah->id,
            'date_of_birth' => $now->copy()->subYears(9)->subMonths(1)->toDateString(),
            'timezone' => 'America/Chicago',
        ]);

        $kids = [$emma, $jake, $lily];
        $parents = [$mike, $sarah];
        $everyone = [$mike, $sarah, $emma, $jake, $lily];

        // ─────────────────────────────────────────────
        //  VAULT CATEGORIES (via seeder)
        // ─────────────────────────────────────────────

        $this->call(VaultCategorySeeder::class);

        // ─────────────────────────────────────────────
        //  TAGS
        // ─────────────────────────────────────────────

        $tags = [];
        $tagDefs = [
            ['name' => 'Chores',     'color' => '#10B981', 'sort_order' => 0],
            ['name' => 'School',     'color' => '#F59E0B', 'sort_order' => 1],
            ['name' => 'Sports',     'color' => '#EF4444', 'sort_order' => 2],
            ['name' => 'Shopping',   'color' => '#8B5CF6', 'sort_order' => 3],
            ['name' => 'Family Fun', 'color' => '#EC4899', 'sort_order' => 4],
            ['name' => 'Yard Work',  'color' => '#059669', 'sort_order' => 5],
            ['name' => 'Pets',       'color' => '#D97706', 'sort_order' => 6],
        ];

        foreach ($tagDefs as $td) {
            $tags[$td['name']] = Tag::create(array_merge($td, ['family_id' => $family->id]));
        }

        // ─────────────────────────────────────────────
        //  RECURRING TASK TEMPLATES
        // ─────────────────────────────────────────────

        $recurringTemplates = [
            [
                'title' => 'Take out trash',
                'description' => 'Take trash cans to the curb before 7 AM',
                'priority' => 'medium',
                'points' => 10,
                'is_family_task' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=TU',
                'tags' => ['Chores'],
                'created_by' => $mike->id,
            ],
            [
                'title' => 'Unload dishwasher',
                'description' => 'Empty the clean dishes and put them away',
                'priority' => 'low',
                'points' => 5,
                'is_family_task' => false,
                'assigned_to' => $jake->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Chores'],
                'created_by' => $sarah->id,
            ],
            [
                'title' => 'Walk Biscuit',
                'description' => 'Take Biscuit for a 20-minute walk around the block',
                'priority' => 'medium',
                'points' => 10,
                'is_family_task' => false,
                'assigned_to' => $emma->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Pets'],
                'created_by' => $mike->id,
            ],
            [
                'title' => 'Clean your room',
                'description' => 'Make bed, pick up clothes, organize desk',
                'priority' => 'medium',
                'points' => 10,
                'is_family_task' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SA',
                'tags' => ['Chores'],
                'created_by' => $sarah->id,
            ],
            [
                'title' => 'Mow the lawn',
                'description' => 'Front and back yard — check gas level first',
                'priority' => 'high',
                'points' => 25,
                'is_family_task' => false,
                'assigned_to' => $emma->id,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SA',
                'tags' => ['Yard Work'],
                'created_by' => $mike->id,
            ],
            [
                'title' => 'Feed Biscuit',
                'description' => '1 cup dry food morning and evening',
                'priority' => 'high',
                'points' => 5,
                'is_family_task' => false,
                'assigned_to' => $lily->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Pets'],
                'created_by' => $sarah->id,
            ],
        ];

        // Helper to attach tags with UUID id
        $attachTags = function (Task $task, array $tagNames) use ($tags) {
            foreach ($tagNames as $tn) {
                if (isset($tags[$tn])) {
                    $task->tags()->attach($tags[$tn]->id, ['id' => Str::uuid()]);
                }
            }
        };

        $templateModels = [];
        foreach ($recurringTemplates as $rt) {
            $tagNames = $rt['tags'];
            unset($rt['tags']);
            $task = Task::create(array_merge($rt, ['family_id' => $family->id]));
            $attachTags($task, $tagNames);
            $templateModels[$task->title] = $task;
        }

        // ─────────────────────────────────────────────
        //  3 MONTHS OF COMPLETED TASKS & POINT TRANSACTIONS
        // ─────────────────────────────────────────────

        // Generate realistic completed tasks spread over ~90 days
        $completedTaskDefs = [
            // ── Week 1-2 (about 75-90 days ago) ──
            ['title' => 'Set up family hub accounts', 'assignee' => $mike, 'points' => 15, 'days_ago' => 89, 'priority' => 'high', 'tags' => ['Family Fun']],
            ['title' => 'Vacuum living room', 'assignee' => $emma, 'points' => 10, 'days_ago' => 88, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Fold laundry', 'assignee' => $jake, 'points' => 10, 'days_ago' => 87, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Water the garden', 'assignee' => $lily, 'points' => 5, 'days_ago' => 86, 'priority' => 'low', 'tags' => ['Yard Work']],
            ['title' => 'Help Lily with math homework', 'assignee' => $emma, 'points' => 15, 'days_ago' => 85, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean out garage', 'assignee' => $mike, 'points' => 20, 'days_ago' => 84, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Organize pantry', 'assignee' => $sarah, 'points' => 15, 'days_ago' => 83, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Pick up sticks in yard', 'assignee' => $jake, 'points' => 10, 'days_ago' => 82, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Practice piano — 30 min', 'assignee' => $lily, 'points' => 10, 'days_ago' => 81, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Wipe down kitchen counters', 'assignee' => $emma, 'points' => 5, 'days_ago' => 80, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Fix leaky faucet', 'assignee' => $mike, 'points' => 20, 'days_ago' => 79, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Sort recycling', 'assignee' => $jake, 'points' => 5, 'days_ago' => 78, 'priority' => 'low', 'tags' => ['Chores']],

            // ── Week 3-4 (about 65-77 days ago) ──
            ['title' => 'Wash the car', 'assignee' => $emma, 'points' => 15, 'days_ago' => 76, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Return library books', 'assignee' => $sarah, 'points' => 5, 'days_ago' => 75, 'priority' => 'low', 'tags' => ['School']],
            ['title' => 'Sweep front porch', 'assignee' => $lily, 'points' => 5, 'days_ago' => 74, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Homework — science project poster', 'assignee' => $jake, 'points' => 20, 'days_ago' => 73, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Grocery shopping', 'assignee' => $sarah, 'points' => 10, 'days_ago' => 72, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Mop kitchen floor', 'assignee' => $emma, 'points' => 10, 'days_ago' => 71, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Clean bathroom', 'assignee' => $jake, 'points' => 15, 'days_ago' => 70, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Rake leaves — front yard', 'assignee' => $emma, 'points' => 15, 'days_ago' => 69, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Set up sprinkler timer', 'assignee' => $mike, 'points' => 10, 'days_ago' => 68, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Practice spelling words', 'assignee' => $lily, 'points' => 10, 'days_ago' => 67, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Dust all bookshelves', 'assignee' => $jake, 'points' => 10, 'days_ago' => 66, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Take Biscuit to the vet', 'assignee' => $sarah, 'points' => 15, 'days_ago' => 65, 'priority' => 'high', 'tags' => ['Pets']],

            // ── Month 2 (about 35-64 days ago) ──
            ['title' => 'Clean windows — downstairs', 'assignee' => $emma, 'points' => 15, 'days_ago' => 62, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize bookshelf', 'assignee' => $lily, 'points' => 10, 'days_ago' => 60, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Soccer gear — clean & organize', 'assignee' => $jake, 'points' => 10, 'days_ago' => 58, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Study for history test', 'assignee' => $emma, 'points' => 15, 'days_ago' => 56, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Bake cookies for school bake sale', 'assignee' => $sarah, 'points' => 15, 'days_ago' => 55, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Replace air filters', 'assignee' => $mike, 'points' => 10, 'days_ago' => 54, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Weed the flower beds', 'assignee' => $emma, 'points' => 15, 'days_ago' => 52, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Help Jake with book report', 'assignee' => $mike, 'points' => 10, 'days_ago' => 50, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Vacuum stairs', 'assignee' => $jake, 'points' => 10, 'days_ago' => 48, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Paint bedroom accent wall', 'assignee' => $emma, 'points' => 25, 'days_ago' => 46, 'priority' => 'high', 'tags' => ['Family Fun']],
            ['title' => 'Family game night setup', 'assignee' => $lily, 'points' => 5, 'days_ago' => 44, 'priority' => 'low', 'tags' => ['Family Fun']],
            ['title' => 'Pick up prescriptions', 'assignee' => $sarah, 'points' => 5, 'days_ago' => 42, 'priority' => 'low', 'tags' => ['Shopping']],
            ['title' => 'Tighten wobbly chair', 'assignee' => $mike, 'points' => 5, 'days_ago' => 41, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Trim hedges', 'assignee' => $mike, 'points' => 15, 'days_ago' => 40, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Read 2 chapters of novel', 'assignee' => $jake, 'points' => 10, 'days_ago' => 39, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean out fridge', 'assignee' => $sarah, 'points' => 10, 'days_ago' => 38, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize sports equipment', 'assignee' => $jake, 'points' => 10, 'days_ago' => 36, 'priority' => 'medium', 'tags' => ['Sports']],

            // ── Month 3 / Recent (0-34 days ago) ──
            ['title' => 'Write thank-you cards', 'assignee' => $lily, 'points' => 10, 'days_ago' => 33, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Deep clean kitchen', 'assignee' => $emma, 'points' => 25, 'days_ago' => 31, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Fix bike tire', 'assignee' => $mike, 'points' => 10, 'days_ago' => 30, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Soccer practice carpool', 'assignee' => $sarah, 'points' => 10, 'days_ago' => 28, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Clean kitchen after dinner', 'assignee' => $jake, 'points' => 10, 'days_ago' => 27, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Prepare for spelling bee', 'assignee' => $lily, 'points' => 15, 'days_ago' => 25, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Batch cook lunches for the week', 'assignee' => $sarah, 'points' => 15, 'days_ago' => 24, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Pressure wash driveway', 'assignee' => $mike, 'points' => 20, 'days_ago' => 22, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Clean out backpack', 'assignee' => $jake, 'points' => 5, 'days_ago' => 21, 'priority' => 'low', 'tags' => ['School']],
            ['title' => 'SAT practice test — section 1', 'assignee' => $emma, 'points' => 20, 'days_ago' => 20, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Water houseplants', 'assignee' => $lily, 'points' => 5, 'days_ago' => 19, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Oil squeaky door hinges', 'assignee' => $mike, 'points' => 5, 'days_ago' => 18, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Wash and fold towels', 'assignee' => $emma, 'points' => 10, 'days_ago' => 16, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize craft supplies', 'assignee' => $lily, 'points' => 10, 'days_ago' => 15, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Homework — math worksheet', 'assignee' => $jake, 'points' => 10, 'days_ago' => 14, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean gutters', 'assignee' => $mike, 'points' => 20, 'days_ago' => 13, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Donate old clothes to Goodwill', 'assignee' => $sarah, 'points' => 10, 'days_ago' => 12, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Brush Biscuit', 'assignee' => $lily, 'points' => 5, 'days_ago' => 11, 'priority' => 'low', 'tags' => ['Pets']],
            ['title' => 'Vacuum all bedrooms', 'assignee' => $jake, 'points' => 15, 'days_ago' => 10, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'SAT practice test — section 2', 'assignee' => $emma, 'points' => 20, 'days_ago' => 9, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Make birthday card for Grandma', 'assignee' => $lily, 'points' => 10, 'days_ago' => 8, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Reorganize tool shed', 'assignee' => $mike, 'points' => 15, 'days_ago' => 7, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Scrub bathtub', 'assignee' => $jake, 'points' => 10, 'days_ago' => 6, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Sort & file school papers', 'assignee' => $emma, 'points' => 10, 'days_ago' => 5, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Meal plan for the week', 'assignee' => $sarah, 'points' => 10, 'days_ago' => 4, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Sweep garage', 'assignee' => $jake, 'points' => 10, 'days_ago' => 3, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Practice piano recital piece', 'assignee' => $lily, 'points' => 15, 'days_ago' => 2, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Wipe down all doorknobs', 'assignee' => $emma, 'points' => 5, 'days_ago' => 1, 'priority' => 'low', 'tags' => ['Chores']],
        ];

        // Create completed tasks and matching point transactions
        $completedTasks = [];
        foreach ($completedTaskDefs as $def) {
            $tagNames = $def['tags'];
            $assignee = $def['assignee'];
            $daysAgo = $def['days_ago'];
            $completedAt = $now->copy()->subDays($daysAgo)->setHour(rand(9, 20))->setMinute(rand(0, 59));

            $task = Task::create([
                'family_id' => $family->id,
                'created_by' => collect($parents)->random()->id,
                'assigned_to' => $assignee->id,
                'title' => $def['title'],
                'priority' => $def['priority'],
                'points' => $def['points'],
                'completed_at' => $completedAt,
                'created_at' => $completedAt->copy()->subHours(rand(1, 48)),
                'updated_at' => $completedAt,
            ]);

            $attachTags($task, $tagNames);

            // Matching point transaction
            PointTransaction::create([
                'family_id' => $family->id,
                'user_id' => $assignee->id,
                'type' => PointTransactionType::TaskCompletion->value,
                'points' => $def['points'],
                'description' => "Completed: {$def['title']}",
                'source_type' => Task::class,
                'source_id' => $task->id,
                'created_at' => $completedAt,
                'updated_at' => $completedAt,
            ]);

            $completedTasks[] = $task;
        }

        // ─────────────────────────────────────────────
        //  PENDING / UPCOMING TASKS
        // ─────────────────────────────────────────────

        $pendingTaskDefs = [
            ['title' => 'Buy new soccer cleats', 'assignee' => $sarah, 'points' => 10, 'due_days' => 1, 'priority' => 'medium', 'tags' => ['Shopping', 'Sports']],
            ['title' => 'Finish English essay', 'assignee' => $emma, 'points' => 20, 'due_days' => 2, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Clean out car', 'assignee' => $mike, 'points' => 10, 'due_days' => 3, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Return Amazon packages', 'assignee' => $sarah, 'points' => 5, 'due_days' => 3, 'priority' => 'low', 'tags' => ['Shopping']],
            ['title' => 'Science fair project — build model', 'assignee' => $jake, 'points' => 25, 'due_days' => 5, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Organize toy closet', 'assignee' => $lily, 'points' => 10, 'due_days' => 4, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Plan family movie night', 'assignee' => null, 'points' => 5, 'due_days' => 6, 'priority' => 'low', 'tags' => ['Family Fun'], 'is_family_task' => true],
            ['title' => 'Mulch flower beds', 'assignee' => $mike, 'points' => 20, 'due_days' => 7, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Schedule Biscuit grooming', 'assignee' => $sarah, 'points' => 5, 'due_days' => 5, 'priority' => 'low', 'tags' => ['Pets']],
            ['title' => 'Study for algebra quiz', 'assignee' => $jake, 'points' => 15, 'due_days' => 2, 'priority' => 'high', 'tags' => ['School']],
        ];

        // One overdue task for realism
        $overdueTask = Task::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'assigned_to' => $emma->id,
            'title' => 'Return library books (overdue!)',
            'priority' => 'high',
            'points' => 5,
            'due_date' => $now->copy()->subDays(2)->toDateString(),
            'created_at' => $now->copy()->subDays(9),
        ]);
        $attachTags($overdueTask, ['School']);

        foreach ($pendingTaskDefs as $def) {
            $tagNames = $def['tags'];
            unset($def['tags']);

            $task = Task::create([
                'family_id' => $family->id,
                'created_by' => collect($parents)->random()->id,
                'assigned_to' => $def['assignee']?->id,
                'title' => $def['title'],
                'priority' => $def['priority'],
                'points' => $def['points'],
                'due_date' => $now->copy()->addDays($def['due_days'])->toDateString(),
                'is_family_task' => $def['is_family_task'] ?? false,
            ]);

            $attachTags($task, $tagNames);
        }

        // ─────────────────────────────────────────────
        //  KUDOS (sprinkled throughout 3 months)
        // ─────────────────────────────────────────────

        $kudosDefs = [
            // Parents giving kudos to kids
            ['from' => $mike, 'to' => $emma, 'reason' => 'Great job on the SAT prep!', 'days_ago' => 20],
            ['from' => $sarah, 'to' => $jake, 'reason' => 'Love how you cleaned without being asked', 'days_ago' => 27],
            ['from' => $mike, 'to' => $lily, 'reason' => 'Beautiful piano practice today', 'days_ago' => 15],
            ['from' => $sarah, 'to' => $emma, 'reason' => 'Thanks for helping with dinner', 'days_ago' => 45],
            ['from' => $mike, 'to' => $jake, 'reason' => 'Awesome science project work', 'days_ago' => 58],
            ['from' => $sarah, 'to' => $lily, 'reason' => 'So proud of your spelling bee prep', 'days_ago' => 25],
            ['from' => $mike, 'to' => $emma, 'reason' => 'Kitchen was spotless, well done!', 'days_ago' => 31],
            ['from' => $sarah, 'to' => $jake, 'reason' => 'Nice work vacuuming!', 'days_ago' => 10],
            ['from' => $mike, 'to' => $lily, 'reason' => 'Way to take initiative with the plants', 'days_ago' => 19],
            ['from' => $sarah, 'to' => $emma, 'reason' => 'Excellent essay draft', 'days_ago' => 5],
            // Kids giving kudos to each other
            ['from' => $emma, 'to' => $jake, 'reason' => 'Thanks for being quiet during my study time', 'days_ago' => 14],
            ['from' => $jake, 'to' => $lily, 'reason' => 'You did awesome at piano!', 'days_ago' => 2],
            ['from' => $lily, 'to' => $emma, 'reason' => 'Thanks for helping me with homework', 'days_ago' => 33],
            ['from' => $emma, 'to' => $lily, 'reason' => 'Love the card you made for Grandma', 'days_ago' => 8],
            // Parents to parents
            ['from' => $mike, 'to' => $sarah, 'reason' => 'Amazing meal prep this week', 'days_ago' => 24],
            ['from' => $sarah, 'to' => $mike, 'reason' => 'Driveway looks incredible!', 'days_ago' => 22],
        ];

        foreach ($kudosDefs as $k) {
            $createdAt = $now->copy()->subDays($k['days_ago'])->setHour(rand(17, 21));

            PointTransaction::create([
                'family_id' => $family->id,
                'user_id' => $k['to']->id,
                'type' => PointTransactionType::Kudos->value,
                'points' => 1,
                'description' => "Kudos from {$k['from']->name}: {$k['reason']}",
                'awarded_by' => $k['from']->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // ─────────────────────────────────────────────
        //  REWARDS
        // ─────────────────────────────────────────────

        $sweets = Reward::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => 'Sweets',
            'description' => 'Pick a treat from the candy stash',
            'point_cost' => 10,
            'icon' => 'cookie',
            'sort_order' => 0,
        ]);

        $screenTime = Reward::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Extra Screen Time (30 min)',
            'description' => 'Extra 30 minutes of screen time',
            'point_cost' => 20,
            'icon' => 'tv',
            'sort_order' => 1,
        ]);

        $pickDinner = Reward::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Pick Dinner',
            'description' => 'Choose what the family has for dinner',
            'point_cost' => 30,
            'icon' => 'pizza',
            'sort_order' => 2,
        ]);

        $moviePick = Reward::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => 'Movie Night Pick',
            'description' => 'Choose the family movie night film',
            'point_cost' => 40,
            'icon' => 'film',
            'sort_order' => 3,
        ]);

        $stayUpLate = Reward::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => 'Stay Up Late',
            'description' => 'Stay up 1 hour past bedtime',
            'point_cost' => 75,
            'icon' => 'moon',
            'sort_order' => 4,
        ]);

        $friendSleepover = Reward::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Friend Sleepover',
            'description' => 'Invite a friend for a sleepover this weekend',
            'point_cost' => 100,
            'icon' => 'house',
            'sort_order' => 5,
        ]);

        $skipChoreDay = Reward::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => 'Skip Chore Day',
            'description' => 'Get a free pass on one day of chores',
            'point_cost' => 50,
            'icon' => 'confetti',
            'sort_order' => 6,
        ]);

        // ─────────────────────────────────────────────
        //  REWARD PURCHASES (with matching point transactions)
        // ─────────────────────────────────────────────

        $purchases = [
            ['user' => $emma, 'reward' => $sweets, 'days_ago' => 70],
            ['user' => $jake, 'reward' => $sweets, 'days_ago' => 65],
            ['user' => $emma, 'reward' => $screenTime, 'days_ago' => 50],
            ['user' => $lily, 'reward' => $sweets, 'days_ago' => 45],
            ['user' => $jake, 'reward' => $screenTime, 'days_ago' => 35],
            ['user' => $emma, 'reward' => $pickDinner, 'days_ago' => 25],
            ['user' => $lily, 'reward' => $sweets, 'days_ago' => 18],
            ['user' => $jake, 'reward' => $sweets, 'days_ago' => 12],
            ['user' => $emma, 'reward' => $moviePick, 'days_ago' => 7],
        ];

        foreach ($purchases as $p) {
            $purchasedAt = $now->copy()->subDays($p['days_ago'])->setHour(rand(15, 19));

            RewardPurchase::create([
                'family_id' => $family->id,
                'reward_id' => $p['reward']->id,
                'user_id' => $p['user']->id,
                'points_spent' => $p['reward']->point_cost,
                'purchased_at' => $purchasedAt,
                'created_at' => $purchasedAt,
                'updated_at' => $purchasedAt,
            ]);

            PointTransaction::create([
                'family_id' => $family->id,
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

        // ─────────────────────────────────────────────
        //  DEDUCTIONS (a couple for realism)
        // ─────────────────────────────────────────────

        $deductionAt = $now->copy()->subDays(40)->setHour(19);
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $jake->id,
            'type' => PointTransactionType::Deduction->value,
            'points' => -5,
            'description' => 'Left bike in the driveway again',
            'awarded_by' => $mike->id,
            'created_at' => $deductionAt,
            'updated_at' => $deductionAt,
        ]);

        $deductionAt2 = $now->copy()->subDays(15)->setHour(20);
        PointTransaction::create([
            'family_id' => $family->id,
            'user_id' => $emma->id,
            'type' => PointTransactionType::Deduction->value,
            'points' => -5,
            'description' => 'Forgot to walk Biscuit',
            'awarded_by' => $sarah->id,
            'created_at' => $deductionAt2,
            'updated_at' => $deductionAt2,
        ]);

        // ─────────────────────────────────────────────
        //  WELCOME BONUS (adjustment for each kid)
        // ─────────────────────────────────────────────

        foreach ($kids as $kid) {
            $bonusAt = $now->copy()->subDays(90)->setHour(10);
            PointTransaction::create([
                'family_id' => $family->id,
                'user_id' => $kid->id,
                'type' => PointTransactionType::Adjustment->value,
                'points' => 25,
                'description' => 'Welcome to Kinhold bonus!',
                'awarded_by' => $mike->id,
                'created_at' => $bonusAt,
                'updated_at' => $bonusAt,
            ]);
        }

        // ─────────────────────────────────────────────
        //  BADGES
        // ─────────────────────────────────────────────

        // Create default badges using BadgeService (single source of truth)
        $badges = BadgeService::createDefaultBadges($family->id, $mike->id);

        // Custom badges
        $welcomeBadge = Badge::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'name' => 'Welcome to Kinhold!',
            'description' => 'Joined the family hub',
            'icon' => 'shield',
            'color' => '#7d57a8',
            'trigger_type' => BadgeTriggerType::Custom->value,
            'is_hidden' => false,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $superHelper = Badge::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'name' => 'Super Helper',
            'description' => 'Went above and beyond to help the family',
            'icon' => 'thumbs-up',
            'color' => '#f59e0b',
            'trigger_type' => BadgeTriggerType::Custom->value,
            'is_hidden' => false,
            'is_active' => true,
            'sort_order' => 21,
        ]);

        // Award badges to family members
        // Everyone gets Welcome badge
        foreach ($everyone as $member) {
            $welcomeBadge->users()->attach($member->id, [
                'id' => Str::uuid(),
                'earned_at' => $now->copy()->subDays(89),
                'awarded_by' => $mike->id,
            ]);
        }

        // All 3 kids have First Steps
        foreach ($kids as $kid) {
            $badges['First Steps']->users()->attach($kid->id, [
                'id' => Str::uuid(),
                'earned_at' => $now->copy()->subDays(88),
            ]);
        }

        // Emma has Task Rookie (she has ~18 completed tasks)
        $badges['Task Rookie']->users()->attach($emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(56),
        ]);

        // Emma and Jake have Rising Star (100+ points earned)
        $badges['Rising Star']->users()->attach($emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(46),
        ]);
        $badges['Rising Star']->users()->attach($jake->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(36),
        ]);

        // Emma got Super Helper custom badge
        $superHelper->users()->attach($emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(30),
            'awarded_by' => $sarah->id,
        ]);

        // Mike has First Steps too (from completed tasks)
        $badges['First Steps']->users()->attach($mike->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(84),
        ]);

        // Sarah has First Steps
        $badges['First Steps']->users()->attach($sarah->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(83),
        ]);

        // ─────────────────────────────────────────────
        //  VAULT ENTRIES
        // ─────────────────────────────────────────────

        $categories = VaultCategory::where('family_id', $family->id)->get()->keyBy('slug');

        // Medical
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $sarah->id,
            'title' => 'Family Pediatrician',
            'encrypted_data' => $vault->encrypt([
                'Doctor' => 'Dr. Rebecca Chen',
                'Practice' => 'Sunshine Pediatrics',
                'Phone' => '(555) 234-5678',
                'Address' => '4521 Medical Center Dr, Suite 200',
                'Patient Portal' => 'sunshinepeds.myportal.com',
            ]),
            'notes' => 'Annual checkups in August. Lily has a follow-up in April.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $sarah->id,
            'title' => 'Emma — Allergies & Medications',
            'encrypted_data' => $vault->encrypt([
                'Allergies' => 'Penicillin, tree nuts',
                'EpiPen Rx' => 'EpiPen Jr Auto-Injector',
                'Pharmacy' => 'CVS #4892 — (555) 345-6789',
                'Allergist' => 'Dr. Alan Park — (555) 456-7890',
            ]),
            'notes' => 'EpiPen expires Sept 2026. Refill in August.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['medical']->id,
            'created_by' => $mike->id,
            'title' => 'Family Dentist',
            'encrypted_data' => $vault->encrypt([
                'Dentist' => 'Dr. Maria Lopez',
                'Practice' => 'Bright Smiles Family Dental',
                'Phone' => '(555) 567-8901',
                'Next Appointments' => 'Jake & Lily: April 15. Emma: May 3.',
            ]),
            'notes' => 'Jake may need braces evaluation this summer.',
        ]);

        // Financial
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $mike->id,
            'title' => 'Joint Checking Account',
            'encrypted_data' => $vault->encrypt([
                'Bank' => 'First National Bank',
                'Account Number' => '****4829',
                'Routing Number' => '****7631',
                'Online Banking' => 'fnb.com',
                'Login' => 'mjohnson84',
            ]),
            'notes' => 'Primary household account. Auto-pay for mortgage and utilities.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['financial']->id,
            'created_by' => $mike->id,
            'title' => 'College Savings — 529 Plans',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'Vanguard 529',
                'Emma Account' => '****8812',
                'Jake Account' => '****8813',
                'Lily Account' => '****8814',
                'Login' => 'vanguard.com — mjohnson',
            ]),
            'notes' => 'Contributing $200/month per child. Review allocation annually.',
        ]);

        // Insurance
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $mike->id,
            'title' => 'Health Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'Blue Cross Blue Shield',
                'Policy Number' => 'BCBS-****3947',
                'Group Number' => 'GRP-****2281',
                'Member Services' => '1-800-555-0199',
                'Portal' => 'bcbs.com/members',
            ]),
            'notes' => 'Through Mike\'s employer. Open enrollment in November.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $mike->id,
            'title' => 'Auto Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'State Farm',
                'Policy Number' => 'SF-****7722',
                'Agent' => 'Tom Bradley — (555) 678-9012',
                'Vehicles' => '2022 Honda Odyssey, 2020 Toyota RAV4',
            ]),
            'notes' => 'Renewal in July. Emma will need to be added when she gets her license.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['insurance']->id,
            'created_by' => $sarah->id,
            'title' => 'Homeowners Insurance',
            'encrypted_data' => $vault->encrypt([
                'Provider' => 'State Farm',
                'Policy Number' => 'SF-HOME-****3318',
                'Agent' => 'Tom Bradley — (555) 678-9012',
                'Coverage' => '$350,000 dwelling / $100,000 personal property',
            ]),
            'notes' => 'Renewal in September. Consider increasing coverage.',
        ]);

        // Legal
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['legal']->id,
            'created_by' => $mike->id,
            'title' => 'Wills & Estate Plan',
            'encrypted_data' => $vault->encrypt([
                'Attorney' => 'Jennifer Walsh, Esq.',
                'Firm' => 'Walsh & Associates',
                'Phone' => '(555) 789-0123',
                'Last Updated' => 'October 2025',
                'Guardian Designee' => 'Uncle David & Aunt Karen Johnson',
            ]),
            'notes' => 'Review and update in 2027. Copies in safe deposit box at First National.',
        ]);

        // Education
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['education']->id,
            'created_by' => $sarah->id,
            'title' => 'Emma — Westfield High School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Westfield High School',
                'Student ID' => '2024-****8831',
                'Counselor' => 'Mrs. Patricia Adams',
                'Parent Portal' => 'westfield.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
                'GPA' => '3.8',
            ]),
            'notes' => 'Junior year. SAT scheduled for May. Looking at colleges this summer.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['education']->id,
            'created_by' => $sarah->id,
            'title' => 'Jake — Lincoln Middle School',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Lincoln Middle School',
                'Student ID' => '2024-****5547',
                'Counselor' => 'Mr. James Rivera',
                'Parent Portal' => 'lincoln.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
            ]),
            'notes' => '7th grade. Soccer team tryouts in August. Might need math tutor.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['education']->id,
            'created_by' => $sarah->id,
            'title' => 'Lily — Meadowbrook Elementary',
            'encrypted_data' => $vault->encrypt([
                'School' => 'Meadowbrook Elementary',
                'Student ID' => '2024-****2293',
                'Teacher' => 'Mrs. Amanda Foster (4th grade)',
                'Parent Portal' => 'meadowbrook.powerschool.com',
                'Portal Login' => 'sjohnson_parent',
            ]),
            'notes' => '4th grade. Piano recital in May. Spelling bee champion!',
        ]);

        // Personal
        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $mike->id,
            'title' => 'Wi-Fi & Home Network',
            'encrypted_data' => $vault->encrypt([
                'Network Name' => 'JohnsonFamily5G',
                'Password' => 'B1scu1t-Loves-Treats!',
                'Router Admin' => '192.168.1.1 — admin / ****router',
                'ISP' => 'Comcast Xfinity — account ****4412',
                'ISP Support' => '1-800-XFINITY',
            ]),
            'notes' => 'Guest network: JohnsonGuest / Welcome2024',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $sarah->id,
            'title' => 'Streaming Services',
            'encrypted_data' => $vault->encrypt([
                'Netflix' => 'sarah.johnson@email.com',
                'Disney+' => 'mike.johnson@email.com',
                'Spotify Family' => 'mike.johnson@email.com',
                'YouTube Premium' => 'mike.johnson@email.com',
            ]),
            'notes' => 'Netflix and Disney+ are annual plans. Spotify renews monthly on the 15th.',
        ]);

        VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $categories['personal']->id,
            'created_by' => $mike->id,
            'title' => 'Emergency Contacts',
            'encrypted_data' => $vault->encrypt([
                'Grandma Joan' => '(555) 111-2222',
                'Uncle David' => '(555) 333-4444',
                'Aunt Karen' => '(555) 555-6666',
                'Neighbor (Sue Miller)' => '(555) 777-8888',
                'Poison Control' => '1-800-222-1222',
            ]),
            'notes' => 'Grandma Joan is 15 min away. David & Karen are designated guardians.',
        ]);

        // ─────────────────────────────────────────────
        //  CHAT MESSAGES (sample conversation)
        // ─────────────────────────────────────────────

        $chatConvo = [
            ['role' => 'user', 'user' => $mike, 'message' => 'What tasks are due this week?', 'days_ago' => 5, 'hour' => 8],
            ['role' => 'assistant', 'user' => $mike, 'message' => "Here are the tasks due this week for the Johnson family:\n\n• **Emma** — Sort & file school papers (10 pts) — due today\n• **Jake** — Sweep garage (10 pts) — due in 2 days\n• **Lily** — Practice piano recital piece (15 pts) — due in 3 days\n• **Sarah** — Meal plan for the week (10 pts) — due tomorrow\n\nWould you like me to add any new tasks or check on something else?", 'days_ago' => 5, 'hour' => 8],
            ['role' => 'user', 'user' => $sarah, 'message' => "Who's in the lead on the leaderboard?", 'days_ago' => 3, 'hour' => 19],
            ['role' => 'assistant', 'user' => $sarah, 'message' => "Here's the current weekly leaderboard:\n\n**1st — Emma** — 15 pts this week\n**2nd — Jake** — 10 pts this week\n**3rd — Lily** — 15 pts this week\n\nEmma and Lily are neck and neck! Emma has been on a roll with her SAT prep and chores.", 'days_ago' => 3, 'hour' => 19],
            ['role' => 'user', 'user' => $mike, 'message' => "What's the wifi password?", 'days_ago' => 2, 'hour' => 14],
            ['role' => 'assistant', 'user' => $mike, 'message' => "Your Wi-Fi password is: **B1scu1t-Loves-Treats!** (network: JohnsonFamily5G)\n\nGuest network: **JohnsonGuest** / Welcome2024", 'days_ago' => 2, 'hour' => 14],
            ['role' => 'user', 'user' => $emma, 'message' => 'How many points do I have?', 'days_ago' => 1, 'hour' => 16],
            ['role' => 'assistant', 'user' => $emma, 'message' => "You currently have **185 points** in your bank, Emma!\n\nYou've earned a total of 325 points and spent 140 on rewards. Your most recent reward was **Movie Night Pick** (40 pts) last week.\n\nYou're getting close to being able to afford a **Friend Sleepover** (100 pts)!", 'days_ago' => 1, 'hour' => 16],
            ['role' => 'user', 'user' => $sarah, 'message' => "When is Jake's next dentist appointment?", 'days_ago' => 0, 'hour' => 9],
            ['role' => 'assistant', 'user' => $sarah, 'message' => "Jake's next dentist appointment is **April 15** at Bright Smiles Family Dental with Dr. Maria Lopez. Lily also has an appointment the same day.\n\nNote from the vault: Jake may need a braces evaluation this summer.", 'days_ago' => 0, 'hour' => 9],
        ];

        foreach ($chatConvo as $msg) {
            $createdAt = $now->copy()->subDays($msg['days_ago'])->setHour($msg['hour'])->setMinute(rand(0, 30));
            ChatMessage::create([
                'family_id' => $family->id,
                'user_id' => $msg['user']->id,
                'role' => $msg['role'],
                'message' => $msg['message'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // ─────────────────────────────────────────────
        //  FEATURED EVENTS
        // ─────────────────────────────────────────────

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => "Sarah's Birthday",
            'description' => 'Happy Birthday Sarah!',
            'event_date' => $now->copy()->addDays(18)->toDateString(),
            'event_time' => null,
            'icon' => 'cake',
            'color' => '#EC4899',
            'recurrence' => 'yearly',
            'is_active' => true,
            'is_countdown' => true,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Mom & Dad Anniversary',
            'description' => 'Celebrating another wonderful year together',
            'event_date' => $now->copy()->addDays(79)->toDateString(),
            'event_time' => null,
            'icon' => 'heart',
            'color' => '#EF4444',
            'recurrence' => 'yearly',
            'is_active' => true,
            'is_countdown' => false,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => 'Family Game Night',
            'description' => 'Board games and fun for everyone!',
            'event_date' => $now->copy()->next('Friday')->toDateString(),
            'event_time' => '19:00',
            'icon' => 'game',
            'color' => '#8B5CF6',
            'recurrence' => 'weekly',
            'is_active' => true,
            'is_countdown' => false,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Spring Break',
            'description' => 'No school — family trip to the lake!',
            'event_date' => $now->copy()->addDays(14)->toDateString(),
            'event_time' => null,
            'icon' => 'palm',
            'color' => '#F59E0B',
            'recurrence' => 'none',
            'is_active' => true,
            'is_countdown' => true,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $mike->id,
            'title' => "Emma's Birthday",
            'description' => 'Happy 17th Birthday Emma!',
            'event_date' => $now->copy()->addDays(32)->toDateString(),
            'event_time' => null,
            'icon' => 'cake',
            'color' => '#F472B6',
            'recurrence' => 'yearly',
            'is_active' => true,
            'is_countdown' => false,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Soccer Tournament',
            'description' => "Jake's regional soccer tournament — go Wildcats!",
            'event_date' => $now->copy()->addDays(10)->toDateString(),
            'event_time' => '09:00',
            'icon' => 'soccer',
            'color' => '#10B981',
            'recurrence' => 'none',
            'is_active' => true,
            'is_countdown' => true,
        ]);

        FeaturedEvent::create([
            'family_id' => $family->id,
            'created_by' => $sarah->id,
            'title' => 'Science Fair',
            'description' => "Jake's volcano project — judging at 2pm",
            'event_date' => $now->copy()->addDays(21)->toDateString(),
            'event_time' => '14:00',
            'icon' => 'star',
            'color' => '#6366F1',
            'recurrence' => 'none',
            'is_active' => true,
            'is_countdown' => false,
        ]);

        // ─────────────────────────────────────────────
        //  CALENDAR EVENTS (manual/local)
        // ─────────────────────────────────────────────
        // Realistic family calendar events spread across the current month
        // and next 2 weeks so the calendar and dashboard look populated.

        $calendarEvents = [
            // ── Today ──
            ['title' => 'Soccer Practice', 'creator' => $sarah, 'color' => '#10B981', 'days' => 0, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Piano Lesson — Lily', 'creator' => $sarah, 'color' => '#EC4899', 'days' => 0, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],

            // ── This week ──
            ['title' => 'Parent-Teacher Conference', 'creator' => $sarah, 'color' => '#F59E0B', 'days' => 1, 'hour' => 15, 'duration' => 30, 'location' => 'Lincoln Middle School'],
            ['title' => 'Dentist — Jake & Lily', 'creator' => $sarah, 'color' => '#EF4444', 'days' => 2, 'hour' => 10, 'duration' => 60, 'location' => 'Bright Smiles Family Dental'],
            ['title' => 'Date Night', 'creator' => $mike, 'color' => '#EF4444', 'days' => 2, 'hour' => 19, 'duration' => 180, 'location' => 'Trattoria Bella'],
            ['title' => 'Soccer Practice', 'creator' => $sarah, 'color' => '#10B981', 'days' => 3, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Grocery Run', 'creator' => $sarah, 'color' => '#8B5CF6', 'days' => 3, 'hour' => 10, 'duration' => 60, 'location' => 'Costco'],
            ['title' => 'Emma — SAT Prep Class', 'creator' => $mike, 'color' => '#F59E0B', 'days' => 4, 'hour' => 17, 'duration' => 120, 'location' => 'Kumon Learning Center'],
            ['title' => 'Piano Lesson — Lily', 'creator' => $sarah, 'color' => '#EC4899', 'days' => 4, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],

            // ── Next week ──
            ['title' => 'Soccer Practice', 'creator' => $sarah, 'color' => '#10B981', 'days' => 7, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Biscuit — Vet Checkup', 'creator' => $sarah, 'color' => '#D97706', 'days' => 7, 'hour' => 9, 'duration' => 30, 'location' => 'Paws & Claws Veterinary'],
            ['title' => 'Emma — Driving Lesson', 'creator' => $mike, 'color' => '#6366F1', 'days' => 8, 'hour' => 15, 'duration' => 60],
            ['title' => 'School Early Dismissal', 'creator' => $sarah, 'color' => '#F59E0B', 'days' => 9, 'all_day' => true],
            ['title' => 'Piano Lesson — Lily', 'creator' => $sarah, 'color' => '#EC4899', 'days' => 9, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Soccer Game vs Eagles', 'creator' => $sarah, 'color' => '#10B981', 'days' => 10, 'hour' => 10, 'duration' => 120, 'location' => 'County Sports Complex'],
            ['title' => 'Grandma & Grandpa Visit', 'creator' => $mike, 'color' => '#EC4899', 'days' => 11, 'all_day' => true],

            // ── Past events this month (so the calendar month view isn't empty) ──
            ['title' => 'Soccer Practice', 'creator' => $sarah, 'color' => '#10B981', 'days' => -3, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Oil Change — Minivan', 'creator' => $mike, 'color' => '#6B7280', 'days' => -4, 'hour' => 8, 'duration' => 60, 'location' => 'Jiffy Lube'],
            ['title' => 'Piano Lesson — Lily', 'creator' => $sarah, 'color' => '#EC4899', 'days' => -5, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Book Club — Sarah', 'creator' => $sarah, 'color' => '#8B5CF6', 'days' => -6, 'hour' => 19, 'duration' => 120, 'location' => 'Jen\'s House'],
            ['title' => 'Soccer Practice', 'creator' => $sarah, 'color' => '#10B981', 'days' => -7, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Emma — SAT Prep Class', 'creator' => $mike, 'color' => '#F59E0B', 'days' => -8, 'hour' => 17, 'duration' => 120, 'location' => 'Kumon Learning Center'],
            ['title' => 'Family Movie Night', 'creator' => $mike, 'color' => '#8B5CF6', 'days' => -9, 'hour' => 19, 'duration' => 150],
            ['title' => 'Jake — Soccer Game vs Thunder', 'creator' => $sarah, 'color' => '#10B981', 'days' => -10, 'hour' => 10, 'duration' => 120, 'location' => 'County Sports Complex'],
            ['title' => 'Lily — Play Rehearsal', 'creator' => $sarah, 'color' => '#EC4899', 'days' => -12, 'hour' => 15, 'duration' => 120, 'location' => 'Lincoln Elementary Auditorium'],
        ];

        foreach ($calendarEvents as $ce) {
            $isAllDay = $ce['all_day'] ?? false;
            $startDay = $now->copy()->addDays($ce['days']);

            if ($isAllDay) {
                $startTime = $startDay->copy()->startOfDay();
                $endTime = $startDay->copy()->endOfDay();
            } else {
                $startTime = $startDay->copy()->setHour($ce['hour'])->setMinute(0)->setSecond(0);
                $endTime = $startTime->copy()->addMinutes($ce['duration']);
            }

            FamilyEvent::create([
                'family_id' => $family->id,
                'created_by' => $ce['creator']->id,
                'title' => $ce['title'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'all_day' => $isAllDay,
                'location' => $ce['location'] ?? null,
                'color' => $ce['color'],
            ]);
        }
    }
}
