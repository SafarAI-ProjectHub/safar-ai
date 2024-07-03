<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterTeacherRequest extends FormRequest
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
            'phone_number' => ['required', 'string', 'max:25'],
            'country_code' => ['required', 'string', 'max:5'], // Added validation for country code
            'date_of_birth' => ['required', 'date'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/',
            ],

            'country_location' => ['required', 'string', 'max:255'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
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
            'country_code.required' => 'The country code field is required.', // Added validation message for country code
            'date_of_birth.required' => 'The date of birth field is required.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must be at least 8 characters long and contain at least one letter and one number.',
            'country_location.required' => 'The country field is required.',
            'cv.required' => 'The CV is required.',
            'cv.mimes' => 'The CV must be a file of type: pdf, doc, docx.',
            'cv.max' => 'The CV must not be greater than 2MB.',
            'years_of_experience.required' => 'The years of experience field is required.',
        ];
    }
}