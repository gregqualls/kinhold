<?php

namespace App\Models;

use App\Enums\MealSlot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MealPlan extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_id',
        'created_by',
        'week_start',
        'notes',
        'shopping_list_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'week_start' => 'date',
        ];
    }

    /**
     * MealPlan belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * MealPlan belongs to a User (creator).
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * MealPlan has many MealPlanEntries.
     */
    public function entries(): HasMany
    {
        return $this->hasMany(MealPlanEntry::class);
    }

    /**
     * MealPlan belongs to a ShoppingList.
     */
    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    /**
     * Get all entries grouped by date, then by meal_slot.
     *
     * Returns a Collection keyed by date string, each value being a Collection
     * keyed by meal_slot string containing the entries for that slot.
     */
    public function entriesByDay(): Collection
    {
        return $this->entries()
            ->orderBy('date')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn (MealPlanEntry $entry) => $entry->date->toDateString())
            ->map(fn (Collection $dayEntries) => $dayEntries->groupBy(
                fn (MealPlanEntry $entry) => $entry->meal_slot instanceof MealSlot
                    ? $entry->meal_slot->value
                    : (string) $entry->meal_slot
            ));
    }
}
