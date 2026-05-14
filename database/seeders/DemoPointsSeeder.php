<?php

namespace Database\Seeders;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoPointsSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  KUDOS (sprinkled throughout 3 months)
        // ─────────────────────────────────────────────

        // Compute "days ago" values that land within the current week so the
        // leaderboard (default = weekly period) has data to render.
        $startOfWeek = $now->copy()->startOfWeek();
        $daysSinceWeekStart = (int) $startOfWeek->diffInDays($now);
        $thisWeek0 = max(0, $daysSinceWeekStart);          // today
        $thisWeek1 = max(0, $daysSinceWeekStart - 1);      // yesterday (clamped)

        $kudosDefs = [
            // Parents giving kudos to kids
            ['from' => $this->mike, 'to' => $this->emma, 'reason' => 'Great job on the SAT prep!', 'days_ago' => 20],
            ['from' => $this->sarah, 'to' => $this->jake, 'reason' => 'Love how you cleaned without being asked', 'days_ago' => 27],
            ['from' => $this->mike, 'to' => $this->lily, 'reason' => 'Beautiful piano practice today', 'days_ago' => 15],
            ['from' => $this->sarah, 'to' => $this->emma, 'reason' => 'Thanks for helping with dinner', 'days_ago' => 45],
            ['from' => $this->mike, 'to' => $this->jake, 'reason' => 'Awesome science project work', 'days_ago' => 58],
            ['from' => $this->sarah, 'to' => $this->lily, 'reason' => 'So proud of your spelling bee prep', 'days_ago' => 25],
            ['from' => $this->mike, 'to' => $this->emma, 'reason' => 'Kitchen was spotless, well done!', 'days_ago' => 31],
            ['from' => $this->sarah, 'to' => $this->jake, 'reason' => 'Nice work vacuuming!', 'days_ago' => 10],
            ['from' => $this->mike, 'to' => $this->lily, 'reason' => 'Way to take initiative with the plants', 'days_ago' => 19],
            ['from' => $this->sarah, 'to' => $this->emma, 'reason' => 'Excellent essay draft', 'days_ago' => 5],
            // Kids giving kudos to each other
            ['from' => $this->emma, 'to' => $this->jake, 'reason' => 'Thanks for being quiet during my study time', 'days_ago' => 14],
            ['from' => $this->jake, 'to' => $this->lily, 'reason' => 'You did awesome at piano!', 'days_ago' => 2],
            ['from' => $this->lily, 'to' => $this->emma, 'reason' => 'Thanks for helping me with homework', 'days_ago' => 33],
            ['from' => $this->emma, 'to' => $this->lily, 'reason' => 'Love the card you made for Grandma', 'days_ago' => 8],
            // Parents to parents
            ['from' => $this->mike, 'to' => $this->sarah, 'reason' => 'Amazing meal prep this week', 'days_ago' => 24],
            ['from' => $this->sarah, 'to' => $this->mike, 'reason' => 'Driveway looks incredible!', 'days_ago' => 22],
            // ── This week (so the weekly leaderboard has live data) ──
            ['from' => $this->mike,  'to' => $this->emma, 'reason' => 'Crushing it on the AP Chem study guide',  'days_ago' => $thisWeek0],
            ['from' => $this->sarah, 'to' => $this->lily, 'reason' => 'Practiced piano without a reminder',       'days_ago' => $thisWeek0],
            ['from' => $this->mike,  'to' => $this->jake, 'reason' => 'Mowed the lawn before being asked',        'days_ago' => $thisWeek1],
            ['from' => $this->emma,  'to' => $this->lily, 'reason' => 'Read your book to me, that was sweet',     'days_ago' => $thisWeek1],
            ['from' => $this->sarah, 'to' => $this->mike, 'reason' => 'Thanks for handling carpool today',        'days_ago' => $thisWeek0],
        ];

        $createdKudos = [];
        foreach ($kudosDefs as $i => $k) {
            $createdAt = $now->copy()->subDays($k['days_ago'])->setHour(rand(17, 21));

            $createdKudos[$i] = PointTransaction::create([
                'family_id' => $this->familyId(),
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
        //  STACKED KUDOS — family members "+1"ing each other's kudos so
        //  the demo dashboard shows the new stack badges in action.
        // ─────────────────────────────────────────────

        $stackDefs = [
            // Sarah piggybacks on Mike's "SAT prep" kudo to Emma (index 0)
            ['source' => 0, 'stacker' => $this->sarah, 'hours_after' => 2],
            // Mike piggybacks on Sarah's "cleaned without being asked" kudo to Jake (index 1)
            ['source' => 1, 'stacker' => $this->mike, 'hours_after' => 3],
            // Emma also piggybacks on Sarah's kudo to Jake
            ['source' => 1, 'stacker' => $this->emma, 'hours_after' => 5],
            // Mike piggybacks on this-week kudo to Lily (index = count - 4) so the feed shows a current stack
            ['source' => count($kudosDefs) - 4, 'stacker' => $this->mike, 'hours_after' => 1],
        ];

        foreach ($stackDefs as $s) {
            $source = $createdKudos[$s['source']] ?? null;
            if (! $source) {
                continue;
            }
            // Don't let a stacker accidentally +1 their own kudo or one they received
            if ($source->awarded_by === $s['stacker']->id || $source->user_id === $s['stacker']->id) {
                continue;
            }
            $stackedAt = $source->created_at->copy()->addHours($s['hours_after']);

            PointTransaction::create([
                'family_id' => $this->familyId(),
                'user_id' => $source->user_id,
                'type' => PointTransactionType::Kudos->value,
                'points' => 1,
                'description' => $source->description,
                'awarded_by' => $s['stacker']->id,
                'stacked_from_transaction_id' => $source->id,
                'created_at' => $stackedAt,
                'updated_at' => $stackedAt,
            ]);
        }

        // ─────────────────────────────────────────────
        //  DEDUCTIONS (a couple for realism)
        // ─────────────────────────────────────────────

        $deductionAt = $now->copy()->subDays(40)->setHour(19);
        PointTransaction::create([
            'family_id' => $this->familyId(),
            'user_id' => $this->jake->id,
            'type' => PointTransactionType::Deduction->value,
            'points' => -5,
            'description' => 'Left bike in the driveway again',
            'awarded_by' => $this->mike->id,
            'created_at' => $deductionAt,
            'updated_at' => $deductionAt,
        ]);

        $deductionAt2 = $now->copy()->subDays(15)->setHour(20);
        PointTransaction::create([
            'family_id' => $this->familyId(),
            'user_id' => $this->emma->id,
            'type' => PointTransactionType::Deduction->value,
            'points' => -5,
            'description' => 'Forgot to walk Biscuit',
            'awarded_by' => $this->sarah->id,
            'created_at' => $deductionAt2,
            'updated_at' => $deductionAt2,
        ]);

        // ─────────────────────────────────────────────
        //  WELCOME BONUS (adjustment for each kid)
        // ─────────────────────────────────────────────

        foreach ($this->kids() as $kid) {
            $bonusAt = $now->copy()->subDays(90)->setHour(10);
            PointTransaction::create([
                'family_id' => $this->familyId(),
                'user_id' => $kid->id,
                'type' => PointTransactionType::Adjustment->value,
                'points' => 25,
                'description' => 'Welcome to Kinhold bonus!',
                'awarded_by' => $this->mike->id,
                'created_at' => $bonusAt,
                'updated_at' => $bonusAt,
            ]);
        }
    }
}
