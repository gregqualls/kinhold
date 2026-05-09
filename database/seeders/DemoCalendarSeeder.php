<?php

namespace Database\Seeders;

use App\Models\FamilyEvent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoCalendarSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  FEATURED EVENTS
        // ─────────────────────────────────────────────

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => "Marcus's Birthday",
            'description' => 'Happy Birthday Marcus!',
            'start_time' => $now->copy()->addDays(18)->startOfDay(),
            'all_day' => true,
            'icon' => 'cake',
            'color' => '#EC4899',
            'recurrence' => 'yearly',
            'featured_scope' => 'family',
            'is_countdown' => true,
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Anniversary',
            'description' => 'Celebrating another wonderful year together',
            'start_time' => $now->copy()->addDays(79)->startOfDay(),
            'all_day' => true,
            'icon' => 'heart',
            'color' => '#EF4444',
            'recurrence' => 'yearly',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => 'Family Game Night',
            'description' => 'Board games and fun for everyone!',
            'start_time' => $now->copy()->next('Friday')->setHour(19)->setMinute(0),
            'all_day' => true,
            'icon' => 'game',
            'color' => '#8B5CF6',
            'recurrence' => 'weekly',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Spring Break',
            'description' => 'No school — family trip to the lake!',
            'start_time' => $now->copy()->addDays(14)->startOfDay(),
            'all_day' => true,
            'icon' => 'palm',
            'color' => '#F59E0B',
            'recurrence' => 'none',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => "Zara's Birthday",
            'description' => 'Happy 17th Birthday Zara!',
            'start_time' => $now->copy()->addDays(32)->startOfDay(),
            'all_day' => true,
            'icon' => 'cake',
            'color' => '#F472B6',
            'recurrence' => 'yearly',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Soccer Tournament',
            'description' => "Kenji's regional soccer tournament — go Wildcats!",
            'start_time' => $now->copy()->addDays(10)->setHour(9)->setMinute(0),
            'all_day' => true,
            'icon' => 'soccer',
            'color' => '#10B981',
            'recurrence' => 'none',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
            'title' => 'Science Fair',
            'description' => "Kenji's volcano project — judging at 2pm",
            'start_time' => $now->copy()->addDays(21)->setHour(14)->setMinute(0),
            'all_day' => true,
            'icon' => 'star',
            'color' => '#6366F1',
            'recurrence' => 'none',
            'featured_scope' => 'family',
        ]);

        FamilyEvent::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
            'title' => "Naia's Art Show",
            'description' => 'Cedar Ridge Elementary Spring Art Show — Naia has two pieces on display!',
            'start_time' => $now->copy()->addDays(28)->setHour(18)->setMinute(0),
            'all_day' => false,
            'icon' => 'star',
            'color' => '#EC4899',
            'recurrence' => 'none',
            'featured_scope' => 'family',
        ]);

        // ─────────────────────────────────────────────
        //  CALENDAR EVENTS
        // ─────────────────────────────────────────────

        $calendarEvents = [
            // ── Today ──
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 0, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Piano Lesson — Naia', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => 0, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],

            // ── This week ──
            ['title' => 'Parent-Teacher Conference', 'creator' => $this->sarah, 'color' => '#F59E0B', 'days' => 1, 'hour' => 15, 'duration' => 30, 'location' => 'Riverside Middle School'],
            ['title' => 'Dentist — Kenji & Naia', 'creator' => $this->sarah, 'color' => '#EF4444', 'days' => 2, 'hour' => 10, 'duration' => 60, 'location' => 'Bright Smiles Family Dental'],
            ['title' => 'Date Night', 'creator' => $this->mike, 'color' => '#EF4444', 'days' => 2, 'hour' => 19, 'duration' => 180, 'location' => 'Trattoria Bella'],
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 3, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Grocery Run', 'creator' => $this->sarah, 'color' => '#8B5CF6', 'days' => 3, 'hour' => 10, 'duration' => 60, 'location' => 'Costco'],
            ['title' => 'Zara — SAT Prep Class', 'creator' => $this->mike, 'color' => '#F59E0B', 'days' => 4, 'hour' => 17, 'duration' => 120, 'location' => 'Kumon Learning Center'],
            ['title' => 'Piano Lesson — Naia', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => 4, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Naia — Art Class', 'creator' => $this->sarah, 'color' => '#F472B6', 'days' => 5, 'hour' => 10, 'duration' => 60, 'location' => 'Cedar Ridge Art Center'],

            // ── Next week ──
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 7, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Biscuit — Vet Checkup', 'creator' => $this->sarah, 'color' => '#D97706', 'days' => 7, 'hour' => 9, 'duration' => 30, 'location' => 'Paws & Claws Veterinary'],
            ['title' => 'Zara — Driving Lesson', 'creator' => $this->mike, 'color' => '#6366F1', 'days' => 8, 'hour' => 15, 'duration' => 60],
            ['title' => 'School Early Dismissal', 'creator' => $this->sarah, 'color' => '#F59E0B', 'days' => 9, 'all_day' => true],
            ['title' => 'Piano Lesson — Naia', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => 9, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Soccer Game vs Eagles', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 10, 'hour' => 10, 'duration' => 120, 'location' => 'County Sports Complex'],
            ['title' => 'Grandma & Grandpa Visit', 'creator' => $this->mike, 'color' => '#EC4899', 'days' => 11, 'all_day' => true],
            ['title' => 'Naia — Art Class', 'creator' => $this->sarah, 'color' => '#F472B6', 'days' => 12, 'hour' => 10, 'duration' => 60, 'location' => 'Cedar Ridge Art Center'],
            ['title' => 'Marcus — Work Conference', 'creator' => $this->sarah, 'color' => '#6B7280', 'days' => 12, 'hour' => 8, 'duration' => 480, 'location' => 'Downtown Convention Center'],

            // ── Two weeks out ──
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 14, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Adaeze — Book Club', 'creator' => $this->mike, 'color' => '#8B5CF6', 'days' => 15, 'hour' => 19, 'duration' => 120, 'location' => 'Community Library'],
            ['title' => 'Kenji — IEP Meeting', 'creator' => $this->sarah, 'color' => '#F59E0B', 'days' => 16, 'hour' => 9, 'duration' => 60, 'location' => 'Riverside Middle School'],
            ['title' => 'Zara — SAT Prep Class', 'creator' => $this->mike, 'color' => '#F59E0B', 'days' => 18, 'hour' => 17, 'duration' => 120, 'location' => 'Kumon Learning Center'],
            ['title' => 'Piano Lesson — Naia', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => 18, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Soccer Game vs Falcons', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => 19, 'hour' => 10, 'duration' => 120, 'location' => 'County Sports Complex'],
            ['title' => 'Family Brunch', 'creator' => $this->mike, 'color' => '#EC4899', 'days' => 20, 'hour' => 10, 'duration' => 120],

            // ── Past events this month ──
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => -3, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Oil Change — Minivan', 'creator' => $this->mike, 'color' => '#6B7280', 'days' => -4, 'hour' => 8, 'duration' => 60, 'location' => 'Jiffy Lube'],
            ['title' => 'Piano Lesson — Naia', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => -5, 'hour' => 14, 'duration' => 45, 'location' => 'Ms. Chen\'s Studio'],
            ['title' => 'Adaeze — Book Club', 'creator' => $this->sarah, 'color' => '#8B5CF6', 'days' => -6, 'hour' => 19, 'duration' => 120, 'location' => 'Community Library'],
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => -7, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Zara — SAT Prep Class', 'creator' => $this->mike, 'color' => '#F59E0B', 'days' => -8, 'hour' => 17, 'duration' => 120, 'location' => 'Kumon Learning Center'],
            ['title' => 'Family Movie Night', 'creator' => $this->mike, 'color' => '#8B5CF6', 'days' => -9, 'hour' => 19, 'duration' => 150],
            ['title' => 'Kenji — Soccer Game vs Thunder', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => -10, 'hour' => 10, 'duration' => 120, 'location' => 'County Sports Complex'],
            ['title' => 'Naia — Play Rehearsal', 'creator' => $this->sarah, 'color' => '#EC4899', 'days' => -12, 'hour' => 15, 'duration' => 120, 'location' => 'Cedar Ridge Auditorium'],
            ['title' => 'Naia — Art Class', 'creator' => $this->sarah, 'color' => '#F472B6', 'days' => -14, 'hour' => 10, 'duration' => 60, 'location' => 'Cedar Ridge Art Center'],
            ['title' => 'Adaeze — Dentist', 'creator' => $this->mike, 'color' => '#EF4444', 'days' => -15, 'hour' => 11, 'duration' => 60, 'location' => 'Bright Smiles Family Dental'],
            ['title' => 'Marcus — Work Conference', 'creator' => $this->sarah, 'color' => '#6B7280', 'days' => -16, 'hour' => 8, 'duration' => 480, 'location' => 'Downtown Convention Center'],
            ['title' => 'Zara — Driving Lesson', 'creator' => $this->mike, 'color' => '#6366F1', 'days' => -18, 'hour' => 15, 'duration' => 60],
            ['title' => 'Soccer Practice', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => -21, 'hour' => 16, 'duration' => 90, 'location' => 'Riverside Park Field 3'],
            ['title' => 'Family Dinner Out', 'creator' => $this->mike, 'color' => '#EF4444', 'days' => -22, 'hour' => 18, 'duration' => 90, 'location' => 'Pho Palace'],
            ['title' => 'Kenji — Soccer Game vs Storm', 'creator' => $this->sarah, 'color' => '#10B981', 'days' => -24, 'hour' => 10, 'duration' => 120, 'location' => 'Riverside Park Field 1'],
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
                'family_id' => $this->familyId(),
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
