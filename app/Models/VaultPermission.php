<?php

namespace App\Models;

use App\Enums\PermissionLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultPermission extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vault_entry_id',
        'user_id',
        'permission_level',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permission_level' => PermissionLevel::class,
        ];
    }

    /**
     * VaultPermission belongs to a VaultEntry.
     */
    public function vaultEntry(): BelongsTo
    {
        return $this->belongsTo(VaultEntry::class);
    }

    /**
     * VaultPermission belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if permission allows editing.
     */
    public function canEdit(): bool
    {
        return $this->permission_level === PermissionLevel::Edit;
    }

    /**
     * Check if permission allows viewing.
     */
    public function canView(): bool
    {
        return true;
    }
}
