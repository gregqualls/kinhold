<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'sort_order' => $this->sort_order,
            'tasks_count' => $this->when(isset($this->tasks_count), $this->tasks_count),
            'incomplete_tasks_count' => $this->when(isset($this->incomplete_tasks_count), $this->incomplete_tasks_count),
        ];
    }
}
