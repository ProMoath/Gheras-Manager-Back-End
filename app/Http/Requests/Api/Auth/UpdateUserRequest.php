<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        return [
            'name' => 'nullable|string|min:2|max:255',
            'email' => ['nullable' , 'email', Rule::unique('users')->ignore($user->id)], // ignore current email for the user
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|boolean',
            'job_title' => 'nullable|string',
            'weekly_hours' => 'nullable|numeric',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'phone' => ' nullable | string | max:20',
            'age' => 'nullable |integer| between:18,100',
            'country' => 'nullable| string  | max:255',
            'experience' => 'nullable | string | max:2000',
            'telegram_id' =>'nullable | string| min:2| max:50',
            'experience_years' => 'nullable | integer| min:0| max:50',

        ];
    }
}
