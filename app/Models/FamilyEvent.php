<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'created_by',
        'title',
        'description',
        'start_time',
        'end_time',
        'all_day',
        'location',
        'color',
        'recurrence',
        'visibility',
        'featured_scope',
        'is_countdown',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'all_day' => 'boolean',
            'is_countdown' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ──

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ──

    /**
     * Get the next occurrence date for a recurring event.
     * For non-recurring events, returns the original start_time date.
     */
    public function getNextOccurrenceAttribute(): Carbon
    {
        $date = $this->start_time->copy()->startOfDay();
        $today = now()->startOfDay();

        if ($this->recurrence === 'none' || ! $this->recurrence) {
            return $date;
        }

        while ($date->lt($today)) {
            $date = match ($this->recurrence) {
                'yearly' => $date->addYear(),
                'monthly' => $date->addMonth(),
                'weekly' => $date->addWeek(),
                default => $date,
            };
        }

        return $date;
    }

    /**
     * Get all occurrence dates within a given range for a recurring event.
     * Returns an array of Carbon dates.
     *
     * @return Carbon[]
     */
    public function occurrencesInRange(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        if ($this->recurrence === 'none' || ! $this->recurrence) {
            return [$this->start_time->copy()->startOfDay()];
        }

        $dates = [];
        $date = $this->start_time->copy()->startOfDay();
        $start = $rangeStart->copy()->startOfDay();
        $end = $rangeEnd->copy()->endOfDay();

        // Advance to the first occurrence at or after range start
        while ($date->lt($start)) {
            $date = match ($this->recurrence) {
                'yearly' => $date->addYear(),
                'monthly' => $date->addMonth(),
                'weekly' => $date->addWeek(),
                default => $date,
            };
        }

        // Collect all occurrences within the range (cap at 53 to prevent runaway)
        $limit = 53;
        while ($date->lte($end) && $limit-- > 0) {
            $dates[] = $date->copy();
            $date = match ($this->recurrence) {
                'yearly' => $date->addYear(),
                'monthly' => $date->addMonth(),
                'weekly' => $date->addWeek(),
                default => $date,
            };
        }

        return $dates;
    }

    /**
     * Human-readable recurrence label.
     */
    public function getRecurrenceLabelAttribute(): ?string
    {
        return match ($this->recurrence) {
            'yearly' => 'Every year',
            'monthly' => 'Every month',
            'weekly' => 'Every week',
            default => null,
        };
    }

    // ── Helpers ──

    public function isFeatured(): bool
    {
        return $this->featured_scope !== null;
    }

    public function isPersonalFeatured(): bool
    {
        return $this->featured_scope === 'personal';
    }

    public function isFamilyFeatured(): bool
    {
        return $this->featured_scope === 'family';
    }

    /**
     * Determine visibility level for a given user.
     *
     * @return string 'full', 'busy', or 'hidden'
     */
    public function visibilityFor(User $user): string
    {
        // Creator always sees full details
        if ($this->created_by === $user->id) {
            return 'full';
        }

        return match ($this->visibility) {
            'private' => 'hidden',
            'busy' => 'busy',
            default => 'full',
        };
    }

    // ── Scopes ──

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->whereNotNull('featured_scope');
    }

    public function scopeFamilyFeatured(Builder $query): Builder
    {
        return $query->where('featured_scope', 'family');
    }

    public function scopePersonalFeatured(Builder $query, string $userId): Builder
    {
        return $query->where('featured_scope', 'personal')
            ->where('created_by', $userId);
    }
}
