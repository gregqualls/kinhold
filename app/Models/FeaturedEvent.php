<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'created_by',
        'title',
        'description',
        'event_date',
        'event_time',
        'icon',
        'color',
        'recurrence',
        'is_active',
        'is_countdown',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_time' => 'datetime:H:i',
            'is_active' => 'boolean',
            'is_countdown' => 'boolean',
        ];
    }

    /**
     * Get the next occurrence date for a recurring event.
     * For non-recurring events, returns the original event_date.
     */
    public function getNextOccurrenceAttribute(): \Carbon\Carbon
    {
        $date = $this->event_date->copy();
        $today = now()->startOfDay();

        if ($this->recurrence === 'none') {
            return $date;
        }

        // Advance the date until it's today or in the future
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

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
