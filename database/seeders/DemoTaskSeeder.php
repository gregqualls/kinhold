<?php

namespace Database\Seeders;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTaskSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // Helper to attach tags with UUID id
        $attachTags = function (Task $task, array $tagNames) {
            foreach ($tagNames as $tn) {
                if (isset($this->tags[$tn])) {
                    $task->tags()->attach($this->tags[$tn]->id, ['id' => Str::uuid()]);
                }
            }
        };

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
                'created_by' => $this->mike->id,
            ],
            [
                'title' => 'Unload dishwasher',
                'description' => 'Empty the clean dishes and put them away',
                'priority' => 'low',
                'points' => 5,
                'is_family_task' => false,
                'assigned_to' => $this->jake->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Chores'],
                'created_by' => $this->sarah->id,
            ],
            [
                'title' => 'Walk Biscuit',
                'description' => 'Take Biscuit for a 20-minute walk around the block',
                'priority' => 'medium',
                'points' => 10,
                'is_family_task' => false,
                'assigned_to' => $this->emma->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Pets'],
                'created_by' => $this->mike->id,
            ],
            [
                'title' => 'Clean your room',
                'description' => 'Make bed, pick up clothes, organize desk',
                'priority' => 'medium',
                'points' => 10,
                'is_family_task' => true,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SA',
                'tags' => ['Chores'],
                'created_by' => $this->sarah->id,
            ],
            [
                'title' => 'Mow the lawn',
                'description' => 'Front and back yard — check gas level first',
                'priority' => 'high',
                'points' => 25,
                'is_family_task' => false,
                'assigned_to' => $this->emma->id,
                'recurrence_rule' => 'FREQ=WEEKLY;BYDAY=SA',
                'tags' => ['Yard Work'],
                'created_by' => $this->mike->id,
            ],
            [
                'title' => 'Feed Biscuit',
                'description' => '1 cup dry food morning and evening',
                'priority' => 'high',
                'points' => 5,
                'is_family_task' => false,
                'assigned_to' => $this->lily->id,
                'recurrence_rule' => 'FREQ=DAILY',
                'tags' => ['Pets'],
                'created_by' => $this->sarah->id,
            ],
        ];

        foreach ($recurringTemplates as $rt) {
            $tagNames = $rt['tags'];
            unset($rt['tags']);
            $task = Task::create(array_merge($rt, ['family_id' => $this->familyId()]));
            $attachTags($task, $tagNames);
        }

        // ─────────────────────────────────────────────
        //  3 MONTHS OF COMPLETED TASKS & POINT TRANSACTIONS
        // ─────────────────────────────────────────────

        $parents = $this->parents();

        // Generate realistic completed tasks spread over ~90 days
        $completedTaskDefs = [
            // ── Week 1-2 (about 75-90 days ago) ──
            ['title' => 'Set up family hub accounts', 'assignee' => $this->mike, 'points' => 15, 'days_ago' => 89, 'priority' => 'high', 'tags' => ['Family Fun']],
            ['title' => 'Vacuum living room', 'assignee' => $this->emma, 'points' => 10, 'days_ago' => 88, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Fold laundry', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 87, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Water the garden', 'assignee' => $this->lily, 'points' => 5, 'days_ago' => 86, 'priority' => 'low', 'tags' => ['Yard Work']],
            ['title' => 'Help Lily with math homework', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 85, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean out garage', 'assignee' => $this->mike, 'points' => 20, 'days_ago' => 84, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Organize pantry', 'assignee' => $this->sarah, 'points' => 15, 'days_ago' => 83, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Pick up sticks in yard', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 82, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Practice piano — 30 min', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 81, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Wipe down kitchen counters', 'assignee' => $this->emma, 'points' => 5, 'days_ago' => 80, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Fix leaky faucet', 'assignee' => $this->mike, 'points' => 20, 'days_ago' => 79, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Sort recycling', 'assignee' => $this->jake, 'points' => 5, 'days_ago' => 78, 'priority' => 'low', 'tags' => ['Chores']],

            // ── Week 3-4 (about 65-77 days ago) ──
            ['title' => 'Wash the car', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 76, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Return library books', 'assignee' => $this->sarah, 'points' => 5, 'days_ago' => 75, 'priority' => 'low', 'tags' => ['School']],
            ['title' => 'Sweep front porch', 'assignee' => $this->lily, 'points' => 5, 'days_ago' => 74, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Homework — science project poster', 'assignee' => $this->jake, 'points' => 20, 'days_ago' => 73, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Grocery shopping', 'assignee' => $this->sarah, 'points' => 10, 'days_ago' => 72, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Mop kitchen floor', 'assignee' => $this->emma, 'points' => 10, 'days_ago' => 71, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Clean bathroom', 'assignee' => $this->jake, 'points' => 15, 'days_ago' => 70, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Rake leaves — front yard', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 69, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Set up sprinkler timer', 'assignee' => $this->mike, 'points' => 10, 'days_ago' => 68, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Practice spelling words', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 67, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Dust all bookshelves', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 66, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Take Biscuit to the vet', 'assignee' => $this->sarah, 'points' => 15, 'days_ago' => 65, 'priority' => 'high', 'tags' => ['Pets']],

            // ── Month 2 (about 35-64 days ago) ──
            ['title' => 'Clean windows — downstairs', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 62, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize bookshelf', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 60, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Soccer gear — clean & organize', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 58, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Study for history test', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 56, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Bake cookies for school bake sale', 'assignee' => $this->sarah, 'points' => 15, 'days_ago' => 55, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Replace air filters', 'assignee' => $this->mike, 'points' => 10, 'days_ago' => 54, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Weed the flower beds', 'assignee' => $this->emma, 'points' => 15, 'days_ago' => 52, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Help Jake with book report', 'assignee' => $this->mike, 'points' => 10, 'days_ago' => 50, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Vacuum stairs', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 48, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Paint bedroom accent wall', 'assignee' => $this->emma, 'points' => 25, 'days_ago' => 46, 'priority' => 'high', 'tags' => ['Family Fun']],
            ['title' => 'Family game night setup', 'assignee' => $this->lily, 'points' => 5, 'days_ago' => 44, 'priority' => 'low', 'tags' => ['Family Fun']],
            ['title' => 'Pick up prescriptions', 'assignee' => $this->sarah, 'points' => 5, 'days_ago' => 42, 'priority' => 'low', 'tags' => ['Shopping']],
            ['title' => 'Tighten wobbly chair', 'assignee' => $this->mike, 'points' => 5, 'days_ago' => 41, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Trim hedges', 'assignee' => $this->mike, 'points' => 15, 'days_ago' => 40, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Read 2 chapters of novel', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 39, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean out fridge', 'assignee' => $this->sarah, 'points' => 10, 'days_ago' => 38, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize sports equipment', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 36, 'priority' => 'medium', 'tags' => ['Sports']],

            // ── Month 3 / Recent (0-34 days ago) ──
            ['title' => 'Write thank-you cards', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 33, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Deep clean kitchen', 'assignee' => $this->emma, 'points' => 25, 'days_ago' => 31, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'Fix bike tire', 'assignee' => $this->mike, 'points' => 10, 'days_ago' => 30, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Soccer practice carpool', 'assignee' => $this->sarah, 'points' => 10, 'days_ago' => 28, 'priority' => 'medium', 'tags' => ['Sports']],
            ['title' => 'Clean kitchen after dinner', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 27, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Prepare for spelling bee', 'assignee' => $this->lily, 'points' => 15, 'days_ago' => 25, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Batch cook lunches for the week', 'assignee' => $this->sarah, 'points' => 15, 'days_ago' => 24, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Pressure wash driveway', 'assignee' => $this->mike, 'points' => 20, 'days_ago' => 22, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Clean out backpack', 'assignee' => $this->jake, 'points' => 5, 'days_ago' => 21, 'priority' => 'low', 'tags' => ['School']],
            ['title' => 'SAT practice test — section 1', 'assignee' => $this->emma, 'points' => 20, 'days_ago' => 20, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Water houseplants', 'assignee' => $this->lily, 'points' => 5, 'days_ago' => 19, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Oil squeaky door hinges', 'assignee' => $this->mike, 'points' => 5, 'days_ago' => 18, 'priority' => 'low', 'tags' => ['Chores']],
            ['title' => 'Wash and fold towels', 'assignee' => $this->emma, 'points' => 10, 'days_ago' => 16, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Organize craft supplies', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 15, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Homework — math worksheet', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 14, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Clean gutters', 'assignee' => $this->mike, 'points' => 20, 'days_ago' => 13, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Donate old clothes to Goodwill', 'assignee' => $this->sarah, 'points' => 10, 'days_ago' => 12, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Brush Biscuit', 'assignee' => $this->lily, 'points' => 5, 'days_ago' => 11, 'priority' => 'low', 'tags' => ['Pets']],
            ['title' => 'Vacuum all bedrooms', 'assignee' => $this->jake, 'points' => 15, 'days_ago' => 10, 'priority' => 'high', 'tags' => ['Chores']],
            ['title' => 'SAT practice test — section 2', 'assignee' => $this->emma, 'points' => 20, 'days_ago' => 9, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Make birthday card for Grandma', 'assignee' => $this->lily, 'points' => 10, 'days_ago' => 8, 'priority' => 'medium', 'tags' => ['Family Fun']],
            ['title' => 'Reorganize tool shed', 'assignee' => $this->mike, 'points' => 15, 'days_ago' => 7, 'priority' => 'medium', 'tags' => ['Yard Work']],
            ['title' => 'Scrub bathtub', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 6, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Sort & file school papers', 'assignee' => $this->emma, 'points' => 10, 'days_ago' => 5, 'priority' => 'medium', 'tags' => ['School']],
            ['title' => 'Meal plan for the week', 'assignee' => $this->sarah, 'points' => 10, 'days_ago' => 4, 'priority' => 'medium', 'tags' => ['Shopping']],
            ['title' => 'Sweep garage', 'assignee' => $this->jake, 'points' => 10, 'days_ago' => 3, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Practice piano recital piece', 'assignee' => $this->lily, 'points' => 15, 'days_ago' => 2, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Wipe down all doorknobs', 'assignee' => $this->emma, 'points' => 5, 'days_ago' => 1, 'priority' => 'low', 'tags' => ['Chores']],
        ];

        // Create completed tasks and matching point transactions
        foreach ($completedTaskDefs as $def) {
            $tagNames = $def['tags'];
            $assignee = $def['assignee'];
            $daysAgo = $def['days_ago'];
            $completedAt = $now->copy()->subDays($daysAgo)->setHour(rand(9, 20))->setMinute(rand(0, 59));

            $task = Task::create([
                'family_id' => $this->familyId(),
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
                'family_id' => $this->familyId(),
                'user_id' => $assignee->id,
                'type' => PointTransactionType::TaskCompletion->value,
                'points' => $def['points'],
                'description' => "Completed: {$def['title']}",
                'source_type' => Task::class,
                'source_id' => $task->id,
                'created_at' => $completedAt,
                'updated_at' => $completedAt,
            ]);
        }

        // ─────────────────────────────────────────────
        //  PENDING / UPCOMING TASKS
        // ─────────────────────────────────────────────

        $pendingTaskDefs = [
            ['title' => 'Buy new soccer cleats', 'assignee' => $this->sarah, 'points' => 10, 'due_days' => 1, 'priority' => 'medium', 'tags' => ['Shopping', 'Sports']],
            ['title' => 'Finish English essay', 'assignee' => $this->emma, 'points' => 20, 'due_days' => 2, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Clean out car', 'assignee' => $this->mike, 'points' => 10, 'due_days' => 3, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Return Amazon packages', 'assignee' => $this->sarah, 'points' => 5, 'due_days' => 3, 'priority' => 'low', 'tags' => ['Shopping']],
            ['title' => 'Science fair project — build model', 'assignee' => $this->jake, 'points' => 25, 'due_days' => 5, 'priority' => 'high', 'tags' => ['School']],
            ['title' => 'Organize toy closet', 'assignee' => $this->lily, 'points' => 10, 'due_days' => 4, 'priority' => 'medium', 'tags' => ['Chores']],
            ['title' => 'Plan family movie night', 'assignee' => null, 'points' => 5, 'due_days' => 6, 'priority' => 'low', 'tags' => ['Family Fun'], 'is_family_task' => true],
            ['title' => 'Mulch flower beds', 'assignee' => $this->mike, 'points' => 20, 'due_days' => 7, 'priority' => 'high', 'tags' => ['Yard Work']],
            ['title' => 'Schedule Biscuit grooming', 'assignee' => $this->sarah, 'points' => 5, 'due_days' => 5, 'priority' => 'low', 'tags' => ['Pets']],
            ['title' => 'Study for algebra quiz', 'assignee' => $this->jake, 'points' => 15, 'due_days' => 2, 'priority' => 'high', 'tags' => ['School']],
        ];

        // One overdue task for realism
        $overdueTask = Task::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'assigned_to' => $this->emma->id,
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
                'family_id' => $this->familyId(),
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
    }
}
