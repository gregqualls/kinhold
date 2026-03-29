<?php

namespace App\Models;

use App\Enums\FamilyRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'family_id',
        'family_role',
        'is_managed',
        'managed_by',
        'avatar',
        'avatar_color',
        'google_avatar',
        'date_of_birth',
        'timezone',
        'email_preferences',
        'easter_eggs_found',
        'onboarding_completed_at',
    ];

    /**
     * SECURITY: Attributes that should never come from user input.
     * These are only set explicitly in controllers/services.
     */
    protected $guarded_from_request = [
        'family_role',
        'google_id',
        'family_id',
        'is_managed',
        'managed_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'family_role' => FamilyRole::class,
            'date_of_birth' => 'date',
            'is_managed' => 'boolean',
            'email_preferences' => 'json',
            'easter_eggs_found' => 'array',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    /**
     * User belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * The parent user who manages this account (for managed child accounts).
     */
    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    /**
     * Child accounts managed by this parent.
     */
    public function managedChildren(): HasMany
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    /**
     * Check if this is a managed (parent-controlled) account.
     */
    public function isManaged(): bool
    {
        return $this->is_managed;
    }

    /**
     * Get the user's current family as a query builder.
     * Used by controllers: $user->currentFamily()->firstOrFail()
     */
    public function currentFamily()
    {
        return Family::where('id', $this->family_id);
    }

    /**
     * User has many Tasks (created).
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * User has many assigned Tasks.
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * User has many Vault Entries (created).
     */
    public function vaultEntries(): HasMany
    {
        return $this->hasMany(VaultEntry::class, 'created_by');
    }

    /**
     * User has many Vault Permissions.
     */
    public function vaultPermissions(): HasMany
    {
        return $this->hasMany(VaultPermission::class);
    }

    /**
     * User has many Calendar Connections.
     */
    public function calendarConnections(): HasMany
    {
        return $this->hasMany(CalendarConnection::class);
    }

    /**
     * User has many Documents (uploaded).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Check if user is a parent.
     */
    public function isParent(): bool
    {
        return $this->family_role === FamilyRole::Parent;
    }

    /**
     * Check if user is a child.
     */
    public function isChild(): bool
    {
        return $this->family_role === FamilyRole::Child;
    }

    /**
     * Check if user can access a vault entry.
     */
    public function canAccessVaultEntry(VaultEntry $entry): bool
    {
        // Creator can always access
        if ($entry->created_by === $this->id) {
            return true;
        }

        // Check if user has permission
        return $entry->permissions()
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Check if user can edit a vault entry.
     */
    public function canEditVaultEntry(VaultEntry $entry): bool
    {
        // Creator can always edit
        if ($entry->created_by === $this->id) {
            return true;
        }

        // Check if user has edit permission
        return $entry->permissions()
            ->where('user_id', $this->id)
            ->where('permission_level', 'edit')
            ->exists();
    }

    /**
     * User has many point transactions.
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * User has many badges via pivot.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot(['earned_at', 'awarded_by'])
            ->withTimestamps();
    }

    /**
     * Get the user's total point bank balance.
     */
    public function pointBank(): int
    {
        return (int) $this->pointTransactions()->sum('points');
    }

    /**
     * Check if user has sufficient points for a purchase.
     */
    public function hasSufficientPoints(int $cost): bool
    {
        return $this->pointBank() >= $cost;
    }

    /**
     * Get the default email preferences.
     */
    public static function defaultEmailPreferences(): array
    {
        return [
            'email_task_completed' => true,
            'email_task_assigned' => true,
            'email_weekly_digest' => true,
            'email_family_invite' => true,
        ];
    }

    /**
     * Get a specific email preference (defaults to true if not set).
     */
    public function wantsEmail(string $key): bool
    {
        // Managed accounts without email never get emails
        if (!$this->email) {
            return false;
        }

        $prefs = $this->email_preferences ?? self::defaultEmailPreferences();

        return $prefs[$key] ?? true;
    }
}
