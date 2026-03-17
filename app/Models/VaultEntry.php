<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VaultEntry extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_id',
        'vault_category_id',
        'created_by',
        'title',
        'encrypted_data',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'encrypted_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'json',
        ];
    }

    /**
     * VaultEntry belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * VaultEntry belongs to a VaultCategory.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VaultCategory::class, 'vault_category_id');
    }

    /**
     * VaultEntry belongs to a User (creator).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * VaultEntry has many VaultPermissions.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(VaultPermission::class);
    }

    /**
     * VaultEntry has many Documents (polymorphic).
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Check if a user can access this vault entry.
     */
    public function isAccessibleBy(User $user): bool
    {
        // Creator can always access
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check if user has permission
        return $this->permissions()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Check if a user can edit this vault entry.
     */
    public function canBeEditedBy(User $user): bool
    {
        // Creator can always edit
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check if user has edit permission
        return $this->permissions()
            ->where('user_id', $user->id)
            ->where('permission_level', 'edit')
            ->exists();
    }

    /**
     * Grant access to a user.
     */
    public function grantAccess(User $user, string $permissionLevel = 'view'): VaultPermission
    {
        return $this->permissions()->updateOrCreate(
            ['user_id' => $user->id],
            ['permission_level' => $permissionLevel]
        );
    }

    /**
     * Revoke access from a user.
     */
    public function revokeAccess(User $user): bool
    {
        return (bool) $this->permissions()
            ->where('user_id', $user->id)
            ->delete();
    }

    /**
     * Get decrypted data.
     */
    public function getDecryptedData(): array
    {
        if (!$this->encrypted_data) {
            return [];
        }

        return json_decode(decrypt($this->encrypted_data), true) ?? [];
    }

    /**
     * Set encrypted data.
     */
    public function setEncryptedData(array $data): void
    {
        $this->encrypted_data = encrypt(json_encode($data));
    }
}
