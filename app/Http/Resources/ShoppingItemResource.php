<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shopping_list_id' => $this->shopping_list_id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'category' => $this->category,
            'is_checked' => $this->is_checked,
            'checked_by' => $this->checked_by,
            'checked_at' => $this->checked_at,
            'has_on_hand' => $this->has_on_hand,
            'source' => $this->source,
            'source_recipe_id' => $this->source_recipe_id,
            'source_recipe_name' => $this->source_recipe_name,
            'source_ingredient_id' => $this->source_ingredient_id,
            'meal_plan_entry_id' => $this->meal_plan_entry_id,
            'needed_date' => $this->needed_date,
            'notes' => $this->notes,
            'is_recurring' => $this->is_recurring,
            'default_quantity' => $this->default_quantity,
            'sort_order' => $this->sort_order,
            'added_by' => new UserResource($this->whenLoaded('addedBy')),
            'checked_by_user' => new UserResource($this->whenLoaded('checkedByUser')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
