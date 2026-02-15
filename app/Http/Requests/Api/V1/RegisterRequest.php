<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required','string','min:2','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],

            // Optional Fields
            'phone' => ['nullable','string','max:20'],
            'age' => ['nullable','integer','between:18,100'],
            'country' => ['nullable','string','max:255'],

            'experience' => ['nullable'],
            'experience_years' => ['nullable','integer','min:0','max:50'],

            'weekly_hours' => ['nullable','numeric','min:0','max:168'],
            'telegram_id' => ['nullable','string','min:2','max:50'],
            'job_field' => ['nullable','string','max:255'],
            'job_description' => ['nullable'],

            'role_id' => ['nullable','integer','exists:roles,id'],
            'status' => ['nullable','boolean'],


            'teams' => ['nullable','array'],
            'teams.*' => ['integer','exists:teams,id'],
        ];
    }
    public function messages():array
    {
        return [
            'name.required' => 'يجب إدخال إسم المستخدم!',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقا!ً',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابقة!',
            'teams.*.exists' => 'أحد الفرق المختارة غير موجود في النظام.',
        ];
    }
}
