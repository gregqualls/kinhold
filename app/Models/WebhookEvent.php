<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $provider
 * @property string $event_id
 * @property Carbon|null $processed_at
 */
class WebhookEvent extends Model
{
    use HasUuids;

    protected $fillable = ['provider', 'event_id', 'processed_at'];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }
}
