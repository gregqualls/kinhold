<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardBid extends Model
{
    use HasUuids;

    protected $fillable = [
        'family_id',
        'reward_id',
        'user_id',
        'bid_amount',
        'held_points',
        'is_winning',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'bid_amount' => 'integer',
            'held_points' => 'integer',
            'is_winning' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to active (unresolved) bids.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('resolved_at');
    }
}
