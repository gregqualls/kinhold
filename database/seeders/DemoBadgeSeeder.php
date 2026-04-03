<?php

namespace Database\Seeders;

use App\Enums\BadgeTriggerType;
use App\Models\Badge;
use App\Services\BadgeService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoBadgeSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  BADGES
        // ─────────────────────────────────────────────

        // Create default badges using BadgeService (single source of truth)
        $badges = BadgeService::createDefaultBadges($this->familyId(), $this->mike->id);

        // Custom badges
        $welcomeBadge = Badge::create([
            'family_id' => $this->familyId(),
            'created_by' => $this->mike->id,
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
            'family_id' => $this->familyId(),
            'created_by' => $this->sarah->id,
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
        foreach ($this->everyone() as $member) {
            $welcomeBadge->users()->attach($member->id, [
                'id' => Str::uuid(),
                'earned_at' => $now->copy()->subDays(89),
                'awarded_by' => $this->mike->id,
            ]);
        }

        // All 3 kids have First Steps
        foreach ($this->kids() as $kid) {
            $badges['First Steps']->users()->attach($kid->id, [
                'id' => Str::uuid(),
                'earned_at' => $now->copy()->subDays(88),
            ]);
        }

        // Emma has Task Rookie (she has ~18 completed tasks)
        $badges['Task Rookie']->users()->attach($this->emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(56),
        ]);

        // Emma and Jake have Rising Star (100+ points earned)
        $badges['Rising Star']->users()->attach($this->emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(46),
        ]);
        $badges['Rising Star']->users()->attach($this->jake->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(36),
        ]);

        // Emma got Super Helper custom badge
        $superHelper->users()->attach($this->emma->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(30),
            'awarded_by' => $this->sarah->id,
        ]);

        // Mike has First Steps too (from completed tasks)
        $badges['First Steps']->users()->attach($this->mike->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(84),
        ]);

        // Sarah has First Steps
        $badges['First Steps']->users()->attach($this->sarah->id, [
            'id' => Str::uuid(),
            'earned_at' => $now->copy()->subDays(83),
        ]);
    }
}
