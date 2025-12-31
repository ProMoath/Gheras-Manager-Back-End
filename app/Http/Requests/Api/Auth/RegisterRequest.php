<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {


        return [
            'name' => ['required|string|min:2|max:255'],
            'email' => ['required|string|email|max:255|unique:users'],
            'password' => ['required|string|min:8|confirmed'],
            'phone' => ['nullable|string|max:10'],
            'age' => ['nullable|digits_between:18,100'],
            'job_title' => ['nullable|string|max:255'],
            'experience_years' => ['nullable|digits_between:0,50'],
            'telegram_id' => ['nullable|min:2|max:32'],
            'jop_description' => ['nullable|string|max:2000'],
            'role_id' => ['nullable|exists:roles,id'],
        ];

    }
    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال إسم المستخدم!',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقا!ً',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابقة!',
        ];
    }
}
