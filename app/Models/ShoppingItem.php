<?php

namespace App\Models;

use App\Enums\ShoppingItemSource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shopping_list_id',
        'family_id',
        'added_by',
        'name',
        'quantity',
        'category',
        'is_checked',
        'checked_by',
        'checked_at',
        'has_on_hand',
        'source',
        'source_recipe_id',
        'source_recipe_name',
        'source_ingredient_id',
        'meal_plan_entry_id',
        'needed_date',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'has_on_hand' => 'boolean',
        'checked_at' => 'datetime',
        'needed_date' => 'date',
        'source' => ShoppingItemSource::class,
    ];

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function checkedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function sourceRecipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'source_recipe_id');
    }

    public function sourceIngredient(): BelongsTo
    {
        return $this->belongsTo(RecipeIngredient::class, 'source_ingredient_id');
    }
}
