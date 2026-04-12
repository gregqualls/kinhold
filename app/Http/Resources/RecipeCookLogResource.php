<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeCookLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'cooked_at' => $this->cooked_at,
            'servings_made' => $this->servings_made,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
