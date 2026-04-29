<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $family_id
 * @property Carbon $date
 * @property int $message_count
 * @property int $input_tokens
 * @property int $output_tokens
 */
class AiUsageDaily extends Model
{
    use HasUuids;

    protected $table = 'ai_usage_daily';

    protected $fillable = [
        'family_id',
        'date',
        'message_count',
        'input_tokens',
        'output_tokens',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'message_count' => 'integer',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
