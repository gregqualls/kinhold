<?php

namespace App\Models;

use App\Enums\PointTransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string|null $stacked_from_transaction_id
 * @property bool $stacked_by_me
 */
class PointTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'user_id',
        'type',
        'points',
        'description',
        'source_type',
        'source_id',
        'awarded_by',
        'stacked_from_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => PointTransactionType::class,
            'points' => 'integer',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function stackedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'stacked_from_transaction_id');
    }

    public function stacks(): HasMany
    {
        return $this->hasMany(self::class, 'stacked_from_transaction_id');
    }

    public function scopeEarnings(Builder $query): Builder
    {
        return $query->where('points', '>', 0);
    }

    public function scopeSpending(Builder $query): Builder
    {
        return $query->where('points', '<', 0);
    }

    public function scopeInPeriod(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeForFamily(Builder $query, Family $family): Builder
    {
        return $query->where('family_id', $family->id);
    }
}
