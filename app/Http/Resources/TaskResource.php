<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'is_family_task' => (bool) $this->is_family_task,
            'completed' => $this->completed_at !== null,
            'completed_at' => $this->completed_at,
            'sort_order' => $this->sort_order,
            'task_list_id' => $this->task_list_id,
            'assigned_to_id' => $this->assigned_to,
            'points' => $this->points,
            'effective_points' => $this->getEffectivePoints(),
            'recurrence_rule' => $this->recurrence_rule,
            'recurrence_end' => $this->recurrence_end,
            'parent_task_id' => $this->parent_task_id,
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'assignee' => UserResource::make($this->whenLoaded('assignee')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
