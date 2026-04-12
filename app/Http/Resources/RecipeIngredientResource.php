<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'preparation' => $this->preparation,
            'group_name' => $this->group_name,
            'is_optional' => $this->is_optional,
            'sort_order' => $this->sort_order,
        ];
    }
}
