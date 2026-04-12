<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeCookLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'recipe_id',
        'user_id',
        'cooked_at',
        'servings_made',
        'notes',
    ];

    protected $casts = [
        'cooked_at' => 'date',
        'servings_made' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
