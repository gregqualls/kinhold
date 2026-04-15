<?php

namespace App\Models;

use App\Enums\MealSlot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class MealPlanEntry extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'meal_plan_id',
        'recipe_id',
        'restaurant_id',
        'meal_preset_id',
        'date',
        'meal_slot',
        'custom_title',
        'servings',
        'assigned_cooks',
        'notes',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'meal_slot' => MealSlot::class,
            'assigned_cooks' => 'array',
            'servings' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model and register the saving validation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (MealPlanEntry $entry): void {
            $sources = array_filter([
                $entry->recipe_id,
                $entry->restaurant_id,
                $entry->meal_preset_id,
                $entry->custom_title,
            ]);

            if (count($sources) !== 1) {
                throw new InvalidArgumentException(
                    'A MealPlanEntry must have exactly one of: recipe_id, restaurant_id, meal_preset_id, or custom_title.'
                );
            }
        });
    }

    /**
     * MealPlanEntry belongs to a MealPlan.
     */
    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    /**
     * MealPlanEntry belongs to a Recipe.
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * MealPlanEntry belongs to a Restaurant.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * MealPlanEntry belongs to a MealPreset.
     */
    public function preset(): BelongsTo
    {
        return $this->belongsTo(MealPreset::class, 'meal_preset_id');
    }

    /**
     * Get the display title based on whichever source is set.
     */
    public function getDisplayTitleAttribute(): string
    {
        if ($this->recipe_id && $this->recipe) {
            return $this->recipe->title;
        }

        if ($this->restaurant_id && $this->restaurant) {
            return $this->restaurant->name;
        }

        if ($this->meal_preset_id && $this->preset) {
            return $this->preset->label;
        }

        return (string) $this->custom_title;
    }

    /**
     * Get the effective servings — uses the override if set, otherwise falls
     * back to the linked recipe's default servings.
     */
    public function getEffectiveServingsAttribute(): ?int
    {
        if ($this->servings !== null) {
            return $this->servings;
        }

        if ($this->recipe_id && $this->recipe) {
            return $this->recipe->servings;
        }

        return null;
    }
}
