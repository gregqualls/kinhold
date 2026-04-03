<?php

namespace App\Models;

use App\Enums\RewardType;
use App\Enums\RewardVisibility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $point_cost
 * @property int|null $quantity
 * @property int $quantity_purchased
 * @property Carbon|null $expires_at
 * @property RewardVisibility $visibility
 * @property array<string>|null $visible_to
 * @property int|null $min_age
 * @property int|null $max_age
 * @property bool $is_active
 * @property int $sort_order
 * @property RewardType $reward_type
 * @property int|null $min_bid
 * @property Carbon|null $bid_start_at
 * @property Carbon|null $bid_end_at
 */
class Reward extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'created_by',
        'title',
        'description',
        'point_cost',
        'icon',
        'quantity',
        'quantity_purchased',
        'expires_at',
        'visibility',
        'visible_to',
        'min_age',
        'max_age',
        'reward_type',
        'min_bid',
        'bid_start_at',
        'bid_end_at',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'point_cost' => 'integer',
            'quantity' => 'integer',
            'quantity_purchased' => 'integer',
            'expires_at' => 'datetime',
            'visibility' => RewardVisibility::class,
            'visible_to' => 'array',
            'min_age' => 'integer',
            'max_age' => 'integer',
            'reward_type' => RewardType::class,
            'min_bid' => 'integer',
            'bid_start_at' => 'datetime',
            'bid_end_at' => 'datetime',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(RewardPurchase::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(RewardBid::class);
    }

    public function activeBids(): HasMany
    {
        return $this->bids()->whereNull('resolved_at');
    }

    public function isAuction(): bool
    {
        return $this->reward_type === RewardType::Auction;
    }

    public function isBiddingOpen(): bool
    {
        if (! $this->isAuction() || ! $this->is_active) {
            return false;
        }

        // Timed auction: check window
        if ($this->bid_end_at !== null) {
            $started = $this->bid_start_at === null || $this->bid_start_at->isPast();
            $notEnded = $this->bid_end_at->isFuture();

            return $started && $notEnded;
        }

        // Parent-called: open until manually closed (bid_start_at optional)
        $started = $this->bid_start_at === null || $this->bid_start_at->isPast();

        return $started && ! $this->isResolved();
    }

    public function isResolved(): bool
    {
        return $this->bids()->where('is_winning', true)->exists();
    }

    public function highestBid(): ?RewardBid
    {
        /** @var RewardBid|null */
        return $this->activeBids()
            ->orderByDesc('bid_amount')
            ->orderBy('created_at')
            ->first();
    }

    public function currentUserBid(User $user): ?RewardBid
    {
        /** @var RewardBid|null */
        return $this->activeBids()->where('user_id', $user->id)->first();
    }

    /**
     * Get remaining stock. Null means unlimited.
     */
    public function remainingStock(): ?int
    {
        if ($this->quantity === null) {
            return null;
        }

        return max(0, $this->quantity - $this->quantity_purchased);
    }

    /**
     * Check if stock is available (unlimited or remaining > 0).
     */
    public function hasStock(): bool
    {
        return $this->quantity === null || $this->remainingStock() > 0;
    }

    /**
     * Check if reward has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if reward can currently be purchased.
     */
    public function isPurchasable(): bool
    {
        return $this->is_active && ! $this->isExpired() && $this->hasStock();
    }

    /**
     * Scope to only purchasable rewards.
     */
    public function scopePurchasable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('quantity')
                    ->orWhereRaw('quantity > quantity_purchased');
            });
    }

    /**
     * Check if a reward is visible to a specific user.
     */
    public function isVisibleTo(User $user): bool
    {
        // Parents see everything
        if ($user->isParent()) {
            return true;
        }

        // Role-based visibility
        if ($this->visibility === RewardVisibility::ParentOnly) {
            return false;
        }

        if ($this->visibility === RewardVisibility::Specific) {
            if (! is_array($this->visible_to) || ! in_array($user->id, $this->visible_to)) {
                return false;
            }
        }

        // Age-based filtering (only if user has a date_of_birth)
        if ($user->date_of_birth) {
            /** @var Carbon $dob */
            $dob = $user->date_of_birth;
            $age = $dob->age;

            if ($this->min_age !== null && $age < $this->min_age) {
                return false;
            }

            if ($this->max_age !== null && $age > $this->max_age) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope to rewards visible to a specific user.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isParent()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            // Exclude parent_only
            $q->where('visibility', '!=', RewardVisibility::ParentOnly->value);

            // For 'specific' visibility, user must be in visible_to array
            $q->where(function (Builder $inner) use ($user) {
                $inner->where('visibility', '!=', RewardVisibility::Specific->value)
                    ->orWhereJsonContains('visible_to', $user->id);
            });

            // Age filtering
            if ($user->date_of_birth) {
                /** @var Carbon $dob */
                $dob = $user->date_of_birth;
                $age = $dob->age;
                $q->where(function (Builder $ageQ) use ($age) {
                    $ageQ->whereNull('min_age')->orWhere('min_age', '<=', $age);
                });
                $q->where(function (Builder $ageQ) use ($age) {
                    $ageQ->whereNull('max_age')->orWhere('max_age', '>=', $age);
                });
            }
        });
    }
}
