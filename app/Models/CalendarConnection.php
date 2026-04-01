<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarConnection extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'calendar_id',
        'calendar_name',
        'color',
        'is_active',
        'last_synced_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'token_expires_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * CalendarConnection belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is expired.
     */
    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at->isPast();
    }

    /**
     * Check if the connection is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && ! $this->isTokenExpired();
    }

    /**
     * Update the last synced timestamp.
     */
    public function updateLastSynced(): void
    {
        $this->update(['last_synced_at' => now()]);
    }

    /**
     * Revoke the calendar connection.
     */
    public function revoke(): void
    {
        $this->update([
            'is_active' => false,
            'access_token' => null,
            'refresh_token' => null,
        ]);
    }
}
