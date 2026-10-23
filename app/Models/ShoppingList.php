<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'created_by',
        'name',
        'store_name',
        'is_active',
        'completed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Family, $this>
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<ShoppingItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }

    /**
     * @return HasMany<ShoppingItem, $this>
     */
    public function uncheckedItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class)->where('is_checked', false);
    }

    /**
     * @return HasMany<ShoppingItem, $this>
     */
    public function checkedItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class)->where('is_checked', true);
    }
}
