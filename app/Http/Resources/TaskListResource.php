<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskListResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'sort_order' => $this->sort_order,
            'tasks_count' => $this->tasks_count ?? 0,
            'incomplete_tasks_count' => $this->incomplete_tasks_count ?? 0,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
