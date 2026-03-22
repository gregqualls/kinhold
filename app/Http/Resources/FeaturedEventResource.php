<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeaturedEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->next_occurrence->toDateString(),
            'original_date' => $this->event_date->toDateString(),
            'event_time' => $this->event_time?->format('H:i'),
            'icon' => $this->icon,
            'color' => $this->color,
            'recurrence' => $this->recurrence,
            'recurrence_label' => $this->recurrence_label,
            'is_active' => $this->is_active,
            'is_countdown' => $this->is_countdown,
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
