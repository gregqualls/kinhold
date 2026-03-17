<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, HasUuids;

    /**
     * Default points by priority when task.points is null.
     */
    public const PRIORITY_POINTS = [
        'low' => 5,
        'medium' => 10,
        'high' => 20,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_id',
        'task_list_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'due_date',
        'completed_at',
        'priority',
        'sort_order',
        'is_family_task',
        'points',
        'recurrence_rule',
        'recurrence_end',
        'parent_task_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'priority' => TaskPriority::class,
            'is_family_task' => 'boolean',
            'points' => 'integer',
            'recurrence_end' => 'date',
        ];
    }

    /**
     * Task belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Task belongs to a TaskList.
     */
    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }

    /**
     * Task belongs to a User (creator).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Task belongs to a User (assignee).
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope: Get incomplete tasks.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Scope: Get overdue tasks.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->incomplete()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now());
    }

    /**
     * Scope: Get upcoming tasks (next 7 days).
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->incomplete()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now()->date, now()->addDays(7)->date]);
    }

    /**
     * Scope: Get tasks for a specific user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('assigned_to', $user->id);
    }

    /**
     * Scope: Get tasks for a specific family.
     */
    public function scopeForFamily(Builder $query, Family $family): Builder
    {
        return $query->where('family_id', $family->id);
    }

    /**
     * Check if task is complete.
     */
    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->isComplete() && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Mark task as complete.
     */
    public function markComplete(): void
    {
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark task as incomplete.
     */
    public function markIncomplete(): void
    {
        $this->completed_at = null;
        $this->save();
    }

    /**
     * Parent task (for recurring task instances).
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * Generated occurrences of a recurring task.
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    /**
     * Scope: Only template/recurring tasks (not generated instances).
     */
    public function scopeTemplates(Builder $query): Builder
    {
        return $query->whereNotNull('recurrence_rule')->whereNull('parent_task_id');
    }

    /**
     * Get effective points for this task (explicit or priority default).
     */
    public function getEffectivePoints(): int
    {
        if ($this->points !== null) {
            return $this->points;
        }

        return self::PRIORITY_POINTS[$this->priority?->value ?? 'medium'] ?? 10;
    }

    /**
     * Check if this task is a recurring template.
     */
    public function isRecurring(): bool
    {
        return $this->recurrence_rule !== null && $this->parent_task_id === null;
    }
}
