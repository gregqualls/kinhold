<?php

namespace App\Http\Requests\Vault;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GrantPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $familyId = $this->user()->family_id;

        return [
            'user_id' => "required|exists:users,id,family_id,{$familyId}",
            'permission_level' => 'required|in:view,edit',
        ];
    }
}
