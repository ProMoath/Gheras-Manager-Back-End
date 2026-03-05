<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
        return [
            'name' => 'nullable|string|min:3',
            'description' => 'nullable|string',
            'status' => 'nullable|in:new,in_progress,scheduled,issue,docs,done',
            'active' => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'name.min' => 'اسم المشروع يجب أن يكون 3 أحرف على الأقل',
        ];
    }
}
