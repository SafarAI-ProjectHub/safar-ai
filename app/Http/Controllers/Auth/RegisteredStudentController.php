<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\RegisterStudentRequest;
use Spatie\Permission\Models\Role;
use App\Services\MoodleUserService;


class RegisteredStudentController extends Controller
{
    
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterStudentRequest $request)
    {


        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->country_code . $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'country_location' => $request->country_location,
            'status' => 'pending',
        ]);
        $user->assignRole('Student');
        Student::create([
            'student_id' => $user->id,
            'english_proficiency_level' => 1,
            'subscription_status' => 'free',
        ]);
        $moodleUserService = app(MoodleUserService::class);

            $moodleUserId = $moodleUserService->createUser($user);
            if ($moodleUserId) {
                $user->update(['moodle_id' => $moodleUserId]);
                Log::info("✅ تم تسجيل الطالب في Moodle بنجاح: {$user->email}");
            } else {
                Log::warning("⚠️ فشل تسجيل الطالب في Moodle: {$user->email}");
            }
        
 
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Your account has been created successfully. Please login to continue.',
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