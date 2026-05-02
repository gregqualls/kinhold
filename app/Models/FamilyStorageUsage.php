<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Denormalized per-family storage total. One row per family, kept warm by
 * `Document::created`/`deleted` model events and fully recomputed nightly by
 * the `kinhold:tally-storage` artisan command.
 *
 * @property string $id
 * @property string $family_id
 * @property int $total_bytes
 * @property int $reported_bytes
 * @property Carbon|null $last_calculated_at
 * @property Carbon|null $last_reported_at
 */
class FamilyStorageUsage extends Model
{
    use HasUuids;

    protected $table = 'family_storage_usages';

    protected $fillable = [
        'family_id',
        'total_bytes',
        'reported_bytes',
        'last_calculated_at',
        'last_reported_at',
    ];

    protected function casts(): array
    {
        return [
            'total_bytes' => 'integer',
            'reported_bytes' => 'integer',
            'last_calculated_at' => 'datetime',
            'last_reported_at' => 'datetime',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
