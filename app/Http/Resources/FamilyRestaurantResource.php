<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyRestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant' => new RestaurantResource($this->resource),
            'notes' => $this->pivot?->notes,
            'is_favorite' => isset($this->pivot) ? (bool) $this->pivot->is_favorite : false,
            'created_at' => $this->pivot?->created_at,
        ];
    }
}
