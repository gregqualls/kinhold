<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'description' => 'nullable|string',
            'assigned_to' => "nullable|exists:users,id,family_id,{$this->user()->family_id}",
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'is_family_task' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'recurrence_rule' => 'nullable|string|max:255',
            'recurrence_end' => 'nullable|date',
            'allow_pileup' => 'nullable|boolean',
        ];
    }
}
