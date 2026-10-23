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

    /**
     * @return BelongsTo<Family, $this>
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * @return BelongsTo<Reward, $this>
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
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
