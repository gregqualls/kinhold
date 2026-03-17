<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPurchase extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'reward_id',
        'user_id',
        'points_spent',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'points_spent' => 'integer',
            'purchased_at' => 'datetime',
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
}
