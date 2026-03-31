<?php

namespace App\Models;

use App\Enums\BadgeTriggerType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'family_id',
        'created_by',
        'name',
        'description',
        'icon',
        'custom_icon_path',
        'color',
        'trigger_type',
        'trigger_threshold',
        'is_hidden',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'trigger_type' => BadgeTriggerType::class,
            'trigger_threshold' => 'integer',
            'is_hidden' => 'boolean',
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot(['earned_at', 'awarded_by'])
            ->withTimestamps();
    }

    /**
     * Mask hidden badge details for users who haven't earned them.
     *
     * Shared by both the API controller and MCP tools to ensure
     * consistent hidden badge presentation.
     */
    public static function maskHidden(array $badgeData, bool $isEarned): array
    {
        if (($badgeData['is_hidden'] ?? false) && !$isEarned) {
            return array_merge($badgeData, [
                'name' => '???',
                'description' => 'Hidden badge — complete the challenge to reveal!',
                'icon' => null,
                'color' => '#6b7280',
                'is_earned' => false,
            ]);
        }

        return $badgeData;
    }
}
