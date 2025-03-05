<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

// استدعاء MoodleUserService
use App\Services\MoodleUserService;

class RegisteredTeacherController extends Controller
{
    public function create()
    {
        return view('auth.register-teacher');
    }

    public function store(RegisterTeacherRequest $request)
    {
        $data = $request->validated();
        // دمج كود الدولة مع رقم الهاتف
        $phone_number = $data['country_code'] . $data['phone_number'];

        // رفع الملف إن وجد (السيرة الذاتية)
        if ($request->hasFile('cv')) {
            $cvFile     = $request->file('cv');
            $cvFileName = $cvFile->getClientOriginalName() . '_' . time() . '.' . $cvFile->getClientOriginalExtension();
            $cvPath     = "storage/" . $cvFile->storeAs('cvs', $cvFileName, 'public');
        }

        $user = User::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'email'            => $data['email'],
            'phone_number'     => $phone_number,
            'date_of_birth'    => $data['date_of_birth'],
            'password'         => Hash::make($data['password']),
            'country_location' => $data['country_location'],
            // تم التعديل هنا: كانت 'pending' وأصبحت 'active'
            'status'           => 'active',
        ]);
        $user->assignRole('Teacher');

        Teacher::create([
            'teacher_id'          => $user->id,
            'cv_link'             => $cvPath ?? null,
            'years_of_experience' => $data['years_of_experience'],
            'approval_status'     => 'pending', // لا يزال pending حتى يراجع الأدمن
        ]);

        // تسجيل المعلم في Moodle
        $moodleUserService = app(MoodleUserService::class);
        $moodleUserId = $moodleUserService->createUser($user);

        if ($moodleUserId) {
            $user->update(['moodle_id' => $moodleUserId]);
            Log::info("✅ تم تسجيل المعلم في Moodle بنجاح: {$user->email}");
        } else {
            Log::warning("⚠️ فشل تسجيل المعلم في Moodle: {$user->email}");
        }

        if ($user) {
            return response()->json([
                'success'  => true,
                'message'  => 'Your registration is successful. Please wait for the admin to review your approval status as a teacher.',
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
