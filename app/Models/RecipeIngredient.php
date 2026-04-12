<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'recipe_id',
        'name',
        'quantity',
        'unit',
        'preparation',
        'group_name',
        'is_optional',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'is_optional' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
