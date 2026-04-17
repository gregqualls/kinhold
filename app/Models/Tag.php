<?php

namespace App\Models;

use App\Enums\TagScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'name',
        'color',
        'sort_order',
        'scope',
    ];

    protected $casts = [
        'scope' => TagScope::class,
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_tag')->withTimestamps();
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tag')->using(RecipeTag::class)->withTimestamps();
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_tag')->using(RestaurantTag::class)->withTimestamps();
    }
}
