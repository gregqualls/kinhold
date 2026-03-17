<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'family_role' => $this->family_role,
            'role' => $this->family_role, // alias for frontend convenience
            'avatar' => $this->avatar,
            'date_of_birth' => $this->date_of_birth,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
        ];
    }
}
