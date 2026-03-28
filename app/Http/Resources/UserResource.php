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
            'is_managed' => $this->is_managed,
            'managed_by' => $this->managed_by,
            'avatar' => $this->avatar,
            'avatar_color' => $this->avatar_color,
            'google_avatar' => $this->google_avatar,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'timezone' => $this->timezone,
            'onboarding_completed_at' => $this->onboarding_completed_at,
            'created_at' => $this->created_at,
        ];
    }
}
