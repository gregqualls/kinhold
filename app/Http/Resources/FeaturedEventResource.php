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
            'original_date' => $this->start_time->toDateString(),
            'event_time' => $this->start_time->format('H:i') !== '00:00'
                ? $this->start_time->format('H:i')
                : null,
            'icon' => $this->icon,
            'color' => $this->color,
            'recurrence' => $this->recurrence,
            'recurrence_label' => $this->recurrence_label,
            'is_active' => $this->is_active,
            'is_countdown' => $this->is_countdown,
            'featured_scope' => $this->featured_scope,
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
