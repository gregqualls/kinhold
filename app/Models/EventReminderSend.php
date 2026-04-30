<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReminderSend extends Model
{
    use HasUuids;

    protected $fillable = [
        'family_event_id',
        'occurrence_date',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'occurrence_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    public function familyEvent(): BelongsTo
    {
        return $this->belongsTo(FamilyEvent::class);
    }
}
