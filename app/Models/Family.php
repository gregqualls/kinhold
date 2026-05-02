<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;

/**
 * @property array<string, mixed>|null $settings
 */
class Family extends Model
{
    use Billable, HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'invite_code',
        'settings',
        'billing_owner_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'json',
        ];
    }

    /**
     * Forensic log line whenever a self-hosted instance creates an Nth (N>=2)
     * family — supplements the SPA banner so violations are also visible in
     * server logs. Hosted Kinhold (self_hosted=false) is unaffected.
     */
    protected static function booted(): void
    {
        static::created(function (Family $family): void {
            if (! (bool) config('kinhold.self_hosted', false)) {
                return;
            }

            $count = static::count();
            if ($count <= 1) {
                return;
            }

            Log::warning('Self-hosted Kinhold instance created an additional family.', [
                'family_id' => $family->id,
                'family_count' => $count,
                'commercial_license_acknowledged' => (bool) config('kinhold.commercial_license_acknowledged', false),
            ]);
        });
    }

    /**
     * Family has many Users (members).
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Family has many Tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Family has many Vault Entries.
     */
    public function vaultEntries(): HasMany
    {
        return $this->hasMany(VaultEntry::class);
    }

    /**
     * Family has many Vault Categories.
     */
    public function vaultCategories(): HasMany
    {
        return $this->hasMany(VaultCategory::class);
    }

    /**
     * Family has many point transactions.
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Family has many rewards.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    /**
     * Family has many badges.
     */
    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class);
    }

    /**
     * Family has many MealPlans.
     */
    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    /**
     * Family has many MealPresets.
     */
    public function mealPresets(): HasMany
    {
        return $this->hasMany(MealPreset::class);
    }

    /**
     * Family belongs to many Restaurants (via family_restaurants pivot).
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'family_restaurants')
            ->withPivot('notes', 'is_favorite')
            ->withTimestamps();
    }

    /**
     * Denormalized current storage total — see StorageMeteringService (#216).
     */
    public function storageUsage(): HasOne
    {
        return $this->hasOne(FamilyStorageUsage::class);
    }

    /**
     * Get the leaderboard period setting.
     */
    public function getLeaderboardPeriod(): string
    {
        return $this->settings['leaderboard_period'] ?? 'weekly';
    }

    /**
     * Get the week start day for meal planning (Carbon::MONDAY or Carbon::SUNDAY).
     */
    public function getWeekStartDay(): int
    {
        $day = $this->settings['week_start_day'] ?? 'monday';

        return $day === 'sunday' ? Carbon::SUNDAY : Carbon::MONDAY;
    }

    /**
     * Get enabled modules (legacy helper — returns module names that are globally enabled).
     */
    public function getEnabledModules(): array
    {
        return $this->settings['enabled_modules'] ?? ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges', 'food'];
    }

    /**
     * Get the task assignment setting.
     *
     * Returns ['mode' => 'all'|'parents_only'|'users', 'users' => [...]]
     */
    public function getTaskAssignment(): array
    {
        return $this->settings['task_assignment'] ?? [
            'mode' => 'all',
            'users' => [],
        ];
    }

    /**
     * Determine if a user is allowed to assign tasks to other family members.
     *
     * Parents always return true. Children depend on the task_assignment setting.
     */
    public function userCanAssignTasks(User $user): bool
    {
        if ($user->isParent()) {
            return true;
        }

        $setting = $this->getTaskAssignment();
        $mode = $setting['mode'] ?? 'all';

        return match ($mode) {
            'all' => true,
            'parents_only' => false,
            'users' => in_array($user->id, $setting['users'] ?? []),
            default => true,
        };
    }

    /**
     * Get the recipe creation permission setting.
     *
     * Returns ['mode' => 'all'|'parents_only'|'users', 'users' => [...]]
     */
    public function getRecipeCreation(): array
    {
        return $this->settings['recipe_creation'] ?? [
            'mode' => 'all',
            'users' => [],
        ];
    }

    /**
     * Determine if a user is allowed to create/import recipes.
     *
     * Parents always return true. Children depend on the recipe_creation setting.
     */
    public function userCanCreateRecipes(User $user): bool
    {
        if ($user->isParent()) {
            return true;
        }

        $setting = $this->getRecipeCreation();
        $mode = $setting['mode'] ?? 'all';

        return match ($mode) {
            'all' => true,
            'parents_only' => false,
            'users' => in_array($user->id, $setting['users'] ?? []),
            default => true,
        };
    }

    /**
     * All module names the system supports.
     */
    public const MODULES = ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges', 'food'];

    /**
     * Get the module_access config for a given module.
     *
     * Returns the granular access rule if set, otherwise falls back to the
     * legacy `settings.modules` boolean toggle (true → 'all', false → 'off').
     *
     * @return array{mode: string, roles?: string[], users?: string[]}
     */
    public function getModuleAccess(string $module): array
    {
        $settings = $this->settings ?? [];

        // Check for granular module_access first
        if (isset($settings['module_access'][$module])) {
            return $settings['module_access'][$module];
        }

        // Fall back to legacy boolean toggle
        $legacy = $settings['modules'][$module] ?? true;

        return ['mode' => $legacy === false ? 'off' : 'all'];
    }

    /**
     * Check whether a specific user has access to a module.
     *
     * Parents always have access to every module (except when mode is 'off').
     */
    public function userHasModuleAccess(string $module, User $user): bool
    {
        $access = $this->getModuleAccess($module);
        $mode = $access['mode'] ?? 'all';

        if ($mode === 'off') {
            return false;
        }

        if ($mode === 'all') {
            return true;
        }

        if ($user->isParent()) {
            return true;
        }

        if ($mode === 'roles') {
            $allowedRoles = $access['roles'] ?? [];

            return in_array($user->family_role->value, $allowedRoles, true);
        }

        if ($mode === 'users') {
            $allowedUsers = $access['users'] ?? [];

            return in_array($user->id, $allowedUsers, true);
        }

        return false;
    }

    /**
     * Get the full module_access map (all modules), filling in defaults.
     */
    public function getAllModuleAccess(): array
    {
        $result = [];
        foreach (self::MODULES as $mod) {
            $result[$mod] = $this->getModuleAccess($mod);
        }

        return $result;
    }

    /**
     * Get the default points for a given task priority.
     *
     * Falls back to hardcoded defaults if not configured.
     */
    public function getDefaultPoints(string $priority): int
    {
        $defaults = [
            'low' => 5,
            'medium' => 10,
            'high' => 20,
        ];

        $key = 'default_points_'.$priority;

        return (int) ($this->settings[$key] ?? $defaults[$priority] ?? 10);
    }
}
