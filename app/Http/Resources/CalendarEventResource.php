<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'] ?? null,
            'title' => $this['title'] ?? $this['summary'] ?? null,
            'description' => $this['description'] ?? null,
            'start' => $this['start'] ?? null,
            'end' => $this['end'] ?? null,
            'all_day' => $this['all_day'] ?? false,
            'location' => $this['location'] ?? null,
            'user' => $this['user'] ?? null,
            'source' => $this['source'] ?? 'google',
        ];
    }
}
