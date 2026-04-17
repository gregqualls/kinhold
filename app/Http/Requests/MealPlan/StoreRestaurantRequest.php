<?php

namespace App\Http\Requests\MealPlan;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'google_maps_url' => ['nullable', 'url:http,https', 'max:2048'],
            'menu_url' => ['nullable', 'url:http,https', 'max:2048'],
            'cuisine' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
