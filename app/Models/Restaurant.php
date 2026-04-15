<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Restaurant extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'google_maps_url',
        'menu_url',
        'cuisine',
        'address',
        'phone',
        'notes',
    ];

    /**
     * Restaurant belongs to many Families (via family_restaurants pivot).
     */
    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class, 'family_restaurants')
            ->withPivot('notes', 'is_favorite')
            ->withTimestamps();
    }

    /**
     * Restaurant has many Ratings (polymorphic).
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Calculate average rating for users belonging to a specific family.
     */
    public function familyAverageRating(string $familyId): ?float
    {
        $avg = $this->ratings()
            ->whereHas('user', function ($query) use ($familyId) {
                $query->where('family_id', $familyId);
            })
            ->avg('score');

        return $avg !== null ? (float) $avg : null;
    }
}
