<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'date_of_birth',
        'password',
        'country_location',
        'profile_image',
        'role_id',
        'paypal_subscription_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    protected $dates = [
        'zoom_token_expires_at',
    ];
    /**
     * Get the user's full name by concatenating first and last names.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    //example use : $user->full_name


    /**
     * Relationship with Role
     */
    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }

    // User's responses to various quiz questions
    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }

    // Assessments associated with the user
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    // Subscriptions that the user has
    public function userSubscriptions()
    {
        return $this->hasOne(UserSubscription::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscriptions')
            ->withPivot('start_date', 'next_billing_time', 'status')
            ->withTimestamps();
    }


    public function activeSubscription()
    {
        return $this->subscriptions()->wherePivot('end_date', '>', now())->first();
    }

    public function getSubscriptionTypeAttribute()
    {
        $activeSubscription = $this->activeSubscription();
        return $activeSubscription ? $activeSubscription->subscription_type : 'None';
    }

    // Student's relation 
    public function student()
    {
        return $this->hasOne(Student::class, 'student_id');
    }

    //teacher's relation
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'teacher_id');
    }

    // Payments made by the user through their subscriptions
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Subscription::class);
    }

    /**
     * Check if the user is a student.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->hasRole('Student');
    }

    /**
     * Check if the user is a teacher.
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->hasRole('Teacher');
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }



    // User's level test assessments relationship
    public function levelTestAssessments()
    {
        return $this->hasMany(LevelTestAssessment::class);
    }


    public function courses()
    {
        $student = $this->student;
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot('enrollment_date', 'progress')
            ->withTimestamps();
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    public function meetings()
    {
        return $this->hasMany(UserMeeting::class);
    }

    public function getAgeGroup()
    {
        $age = \Carbon\Carbon::parse($this->date_of_birth)->age;
        if ($age <= 5) {
            return '1-5';
        } else if ($age >= 6 && $age <= 10) {
            return '6-10';
        } elseif ($age > 10 && $age <= 14) {
            return '10-14';
        } elseif ($age > 14 && $age <= 18) {
            return '14-18';
        } else {
            return '18+';
        }
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'teacher_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

}