<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|min:3|max:5000',
            'priority' => 'nullable|in:critical,major,minor',
            'status'=> 'nullable|in:open,in_progress,testing,resolved',
            'type'=> 'nullable|in:new,bug',
            'due_date' => 'nullable|date|after:today',
            'project_id' => 'nullable|exists:projects,id',
            'team_id' => 'nullable|exists:teams,id',
            'parent_task_id'=> 'nullable|exists:tasks,id',
            'work_hours' => 'nullable|numeric|min:0|max:168',
            'linked_tasks' => 'nullable|array',
            'linked_tasks.*' => 'exists:tasks,id',
            'assignees_id' => 'nullable|array',
            'assignees_id.*' => 'exists:users,id',

        ];
    }
    public function messages(): array
    {
        return [
            'title.min' => "يجب أن يكون العنوان على الأقل 3 أحرف",
            'title.required' => "يجب إدخال عنوان المهمة",
        ];
    }
}
