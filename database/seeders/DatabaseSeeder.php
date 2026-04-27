<?php

namespace Database\Seeders;

use App\Enums\FamilyRole;
use App\Models\Badge;
use App\Models\ChatMessage;
use App\Models\Family;
use App\Models\FamilyEvent;
use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
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
            FamilyEvent::where('family_id', $existing->id)->delete();
            VaultEntry::where('family_id', $existing->id)->delete();
            VaultCategory::where('family_id', $existing->id)->delete();
            PointTransaction::where('family_id', $existing->id)->delete();
            ChatMessage::where('family_id', $existing->id)->delete();
            $existing->delete();
            $this->command?->info('Cleared existing demo family data.');
        }

        $now = Carbon::now();
        $demoPassword = bcrypt(Str::random(32));

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
            'password' => $demoPassword,
            'family_id' => $family->id,
            'family_role' => FamilyRole::Parent,
            'date_of_birth' => '1984-06-15',
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
            'email_verified_at' => $now,
            'onboarding_completed_at' => $now,
        ]);

        $sarah = User::create([
            'name' => 'Sarah',
            'email' => 'sarah@demo.local',
            'password' => $demoPassword,
            'family_id' => $family->id,
            'family_role' => FamilyRole::Parent,
            'date_of_birth' => '1986-03-22',
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
            'email_verified_at' => $now,
            'onboarding_completed_at' => $now,
        ]);

        User::create([
            'name' => 'Emma',
            'email' => 'emma@demo.local',
            'password' => $demoPassword,
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'date_of_birth' => $now->copy()->subYears(16)->subMonths(3)->toDateString(),
            'timezone' => 'America/Chicago',
            'email_preferences' => User::defaultEmailPreferences(),
            'email_verified_at' => $now,
            'onboarding_completed_at' => $now,
        ]);

        User::create([
            'name' => 'Jake',
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'is_managed' => true,
            'managed_by' => $mike->id,
            'date_of_birth' => $now->copy()->subYears(13)->subMonths(7)->toDateString(),
            'timezone' => 'America/Chicago',
            'onboarding_completed_at' => $now,
        ]);

        User::create([
            'name' => 'Lily',
            'family_id' => $family->id,
            'family_role' => FamilyRole::Child,
            'is_managed' => true,
            'managed_by' => $sarah->id,
            'date_of_birth' => $now->copy()->subYears(9)->subMonths(1)->toDateString(),
            'timezone' => 'America/Chicago',
            'onboarding_completed_at' => $now,
        ]);

        // ─────────────────────────────────────────────
        //  VAULT CATEGORIES (via seeder)
        // ─────────────────────────────────────────────

        $this->call(VaultCategorySeeder::class);

        // ─────────────────────────────────────────────
        //  PRODUCT CATALOG (global, no family_id)
        // ─────────────────────────────────────────────

        $this->call(ProductCatalogSeeder::class);

        // ─────────────────────────────────────────────
        //  TAGS
        // ─────────────────────────────────────────────

        $tagDefs = [
            // Task tags
            ['name' => 'Chores',     'color' => '#10B981', 'sort_order' => 0,  'scope' => 'task'],
            ['name' => 'School',     'color' => '#F59E0B', 'sort_order' => 1,  'scope' => 'task'],
            ['name' => 'Sports',     'color' => '#EF4444', 'sort_order' => 2,  'scope' => 'task'],
            ['name' => 'Shopping',   'color' => '#8B5CF6', 'sort_order' => 3,  'scope' => 'task'],
            ['name' => 'Family Fun', 'color' => '#EC4899', 'sort_order' => 4,  'scope' => 'task'],
            ['name' => 'Yard Work',  'color' => '#059669', 'sort_order' => 5,  'scope' => 'task'],
            ['name' => 'Pets',       'color' => '#D97706', 'sort_order' => 6,  'scope' => 'task'],
            // Food tags (recipes + restaurants)
            ['name' => 'Breakfast',  'color' => '#F59E0B', 'sort_order' => 7,  'scope' => 'food'],
            ['name' => 'Lunch',      'color' => '#10B981', 'sort_order' => 8,  'scope' => 'food'],
            ['name' => 'Dinner',     'color' => '#3B82F6', 'sort_order' => 9,  'scope' => 'food'],
            ['name' => 'Dessert',    'color' => '#EC4899', 'sort_order' => 10, 'scope' => 'food'],
            ['name' => 'Snack',      'color' => '#8B5CF6', 'sort_order' => 11, 'scope' => 'food'],
        ];

        foreach ($tagDefs as $td) {
            Tag::create(array_merge($td, ['family_id' => $family->id]));
        }

        // ─────────────────────────────────────────────
        //  MODULE SEEDERS (order matters)
        // ─────────────────────────────────────────────

        $this->call([
            DemoTaskSeeder::class,      // tasks + task-completion point transactions
            DemoPointsSeeder::class,    // kudos, deductions, welcome bonuses
            DemoRewardSeeder::class,    // rewards + purchases + redemption transactions
            DemoBadgeSeeder::class,     // badge definitions + awards
            DemoVaultSeeder::class,     // vault entries
            DemoChatSeeder::class,      // chat messages
            DemoCalendarSeeder::class,  // featured events + calendar events
            DemoRecipeSeeder::class,    // recipes + ingredients (must run before MealPlan)
            DemoMealPlanSeeder::class,  // meal presets, restaurants, weekly meal plan (links to recipes)
            DemoShoppingSeeder::class,  // shopping lists + items
        ]);
    }
}
