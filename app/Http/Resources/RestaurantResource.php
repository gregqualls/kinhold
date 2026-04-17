<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'google_maps_url' => $this->google_maps_url,
            'menu_url' => $this->menu_url,
            'address' => $this->address,
            'phone' => $this->phone,
            'image_url' => $this->image_url,
            'notes' => $this->notes,
            // Pivot fields are present when the restaurant is loaded via
            // the family_restaurants relationship (BelongsToMany with pivot).
            'is_favorite' => $this->when(
                $this->resource->relationLoaded('families') || isset($this->pivot),
                fn () => isset($this->pivot) ? (bool) $this->pivot->is_favorite : null
            ),
            'family_notes' => $this->when(
                $this->resource->relationLoaded('families') || isset($this->pivot),
                fn () => isset($this->pivot) ? $this->pivot->notes : null
            ),
            'family_average_rating' => $this->when(
                $this->resource->relationLoaded('ratings'),
                fn () => round((float) $this->resource->ratings->avg('score'), 1)
            ),
        ];
    }
}
