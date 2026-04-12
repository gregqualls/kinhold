<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'servings' => $this->servings,
            'prep_time_minutes' => $this->prep_time_minutes,
            'cook_time_minutes' => $this->cook_time_minutes,
            'total_time_minutes' => $this->total_time_minutes,
            'source_url' => $this->source_url,
            'source_type' => $this->source_type,
            'image_path' => $this->image_path,
            'instructions' => $this->instructions,
            'notes' => $this->notes,
            'is_favorite' => $this->is_favorite,
            'family_average_rating' => round((float) $this->familyAverageRating(), 1),
            'user_rating' => $this->userRating($request->user())?->score,
            'ingredients' => RecipeIngredientResource::collection($this->whenLoaded('ingredients')),
            'cook_logs' => RecipeCookLogResource::collection($this->whenLoaded('cookLogs')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
