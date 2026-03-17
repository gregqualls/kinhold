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
     * Get enabled modules.
     */
    public function getEnabledModules(): array
    {
        return $this->settings['enabled_modules'] ?? ['calendar', 'tasks', 'vault', 'chat', 'points', 'badges'];
    }
}
