<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class RegisteredTeacherController extends Controller
{
    public function create()
    {
        return view('auth.register-teacher');
    }

    public function store(RegisterTeacherRequest $request)
    {
        $data = $request->validated();
        // Merge country code and phone number
        $phone_number = $data['country_code'] . $data['phone_number'];

        // Handle file uploads
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvFileName = $cvFile->getClientOriginalName() . '_' . time() . '.' . $cvFile->getClientOriginalExtension();
            $cvPath = "storage/" . $cvFile->storeAs('cvs', $cvFileName, 'public');
        }

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $phone_number,
            'date_of_birth' => $data['date_of_birth'],
            'password' => Hash::make($data['password']),
            'country_location' => $data['country_location'],
            'role_id' => 3,
            'status' => 'pending',
        ]);
        $user->assignRole('Teacher');
        Teacher::create([
            'teacher_id' => $user->id,
            'cv_link' => $cvPath ?? null,
            'years_of_experience' => $data['years_of_experience'],
            'approval_status' => 'pending',
        ]);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Your registration is successful. Please wait for the admin to approve your account.',
                'redirect' => route('login')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again later.'
            ]);
        }
    }
}