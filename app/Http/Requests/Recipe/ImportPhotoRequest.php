<?php

namespace App\Http\Requests\Recipe;

use App\Models\Recipe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImportPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Recipe::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:10240', 'mimes:jpeg,png,heic,heif'],
        ];
    }
}
