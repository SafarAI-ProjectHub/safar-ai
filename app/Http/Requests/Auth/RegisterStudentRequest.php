<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:15'],
            'country_code' => ['required', 'string', 'max:5'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/',
            ],
            'country_location' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone_number.required' => 'The phone number field is required.',
            'country_code.required' => 'The country code field is required.',
            'date_of_birth.required' => 'The date of birth field is required.',
            'date_of_birth.before' => 'The date of birth must be a date before today.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must be at least 8 characters long and contain at least one letter and one number.',
            'country_location.required' => 'The country field is required.',
        ];
    }
}