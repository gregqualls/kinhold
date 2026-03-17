<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaultCategory extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_id',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
    ];

    /**
     * VaultCategory belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * VaultCategory has many VaultEntries.
     */
    public function entries(): HasMany
    {
        return $this->hasMany(VaultEntry::class);
    }

    /**
     * Default vault categories.
     *
     * @return array<string, array<string, string>>
     */
    public static function defaultCategories(): array
    {
        return [
            'medical' => [
                'name' => 'Medical',
                'slug' => 'medical',
                'icon' => 'heart',
                'description' => 'Medical records and health information',
            ],
            'financial' => [
                'name' => 'Financial',
                'slug' => 'financial',
                'icon' => 'dollar-sign',
                'description' => 'Financial accounts and information',
            ],
            'insurance' => [
                'name' => 'Insurance',
                'slug' => 'insurance',
                'icon' => 'shield',
                'description' => 'Insurance policies and documents',
            ],
            'legal' => [
                'name' => 'Legal',
                'slug' => 'legal',
                'icon' => 'briefcase',
                'description' => 'Legal documents and agreements',
            ],
            'education' => [
                'name' => 'Education',
                'slug' => 'education',
                'icon' => 'book',
                'description' => 'Educational records and transcripts',
            ],
            'personal' => [
                'name' => 'Personal',
                'slug' => 'personal',
                'icon' => 'lock',
                'description' => 'Personal documents and information',
            ],
        ];
    }
}
