<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory, HasUuids;

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
     * Family has many Task Lists.
     */
    public function taskLists(): HasMany
    {
        return $this->hasMany(TaskList::class);
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
     * Get the leaderboard period setting.
     */
    public function getLeaderboardPeriod(): string
    {
        return $this->settings['leaderboard_period'] ?? 'weekly';
    }

    /**
     * Get enabled modules (legacy helper — returns module names that are globally enabled).
     */
    public function getEnabledModules(): array
    {
        return $this->settings['enabled_modules'] ?? ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges'];
    }

    /**
     * All module names the system supports.
     */
    public const MODULES = ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges'];

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

        // Module completely disabled — nobody can access it
        if ($mode === 'off') {
            return false;
        }

        // Module open to everyone
        if ($mode === 'all') {
            return true;
        }

        // Parents always have access when the module isn't globally off
        if ($user->isParent()) {
            return true;
        }

        // Role-based access
        if ($mode === 'roles') {
            $allowedRoles = $access['roles'] ?? [];
            return in_array($user->family_role->value, $allowedRoles, true);
        }

        // Per-user access
        if ($mode === 'users') {
            $allowedUsers = $access['users'] ?? [];
            return in_array($user->id, $allowedUsers, true);
        }

        return false;
    }

    /**
     * Get the full module_access map (all modules), filling in defaults.
     *
     * Useful for returning to the frontend so the Settings UI can render the grid.
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

        $key = 'default_points_' . $priority;

        return (int) ($this->settings[$key] ?? $defaults[$priority] ?? 10);
    }
}
