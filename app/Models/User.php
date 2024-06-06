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

    /**
     * Get the user's full name by concatenating first and last names.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Relationship with Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

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
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Student's relation 
    public function student()
    {
        return $this->hasOne(Student::class);
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
        return $this->role->role_name === 'Student';
    }

    /**
     * Check if the user is a teacher.
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->role->role_name === 'Teacher';
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role->role_name === 'Admin';
    }


    // User's level test assessments relationship
    public function levelTestAssessments()
    {
        return $this->hasMany(LevelTestAssessment::class);
    }

    // courses that the user is enrolled in
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot('enrollment_date', 'progress')
            ->withTimestamps();
    }

    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class);
    }


}