<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Services\MoodleUserService;
use Illuminate\Support\Facades\Log;       // لاستخدام Log
use App\Events\UserUpdated;               // لو كنت تستخدم الحدث UserUpdated
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * الحقول القابلة للإسناد الجماعي (fillable).
     * هنا نفترض أنك أضفت الحقول في الجدول: moodle_id, moodle_role_id, moodle_password
     */
    protected $fillable = [
        'moodle_id',
        'moodle_role_id',
        'moodle_password',
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
     * الحقول المخفية عند التحويل إلى JSON/array.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'moodle_password', // إن رغبت بإخفاء كلمة مرور Moodle أيضًا
    ];

    /**
     * الحقول التي سيتم عمل Casting لها آليًا.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'date_of_birth'     => 'date',
    ];

    /**
     * حقول التاريخ.
     */
    protected $dates = [
        'zoom_token_expires_at',
    ];

    /**
     * دالة للحصول على الاسم الكامل (first + last).
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * علاقات خاصة بالأسئلة والاختبارات، إلخ.
     */
    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * علاقة مع الـSubscriptions
     */
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

    /**
     * علاقات الطالب أو المعلّم
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'student_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'teacher_id');
    }

    /**
     * علاقات مع المدفوعات
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Subscription::class);
    }

    /**
     * الصلاحيات السريعة
     */
    public function isStudent()
    {
        return $this->hasRole('Student');
    }
    public function isTeacher()
    {
        return $this->hasRole('Teacher');
    }
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * اختبارات المستوى
     */
    public function levelTestAssessments()
    {
        return $this->hasMany(LevelTestAssessment::class);
    }

    /**
     * علاقة بالمقررات التي يدرسها (إذا كان Student)
     */
    public function courses()
    {
        // إذا اعتمدت جدولة (course_student) حيث student_id هو user->id
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
                    ->withPivot('enrollment_date', 'progress')
                    ->withTimestamps();
    }

    /**
     * التقييمات (Rates) أو تقييم المستخدم لدورات مثلاً
     */
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * اجتماعات Zoom مرتبطة بالمستخدم
     */
    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    public function meetings()
    {
        return $this->hasMany(UserMeeting::class);
    }

    /**
     * عقود المعلمين
     */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'teacher_id');
    }

    /**
     * سجلات الأنشطة
     */
    public function timeLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * تحسب الفئة العمرية اعتمادًا على تاريخ الميلاد
     */
    public function getAgeGroup()
    {
        $age = Carbon::parse($this->date_of_birth)->age;
        if ($age <= 5) {
            return '1-5';
        } elseif ($age >= 6 && $age <= 10) {
            return '6-10';
        } elseif ($age > 10 && $age <= 14) {
            return '10-14';
        } elseif ($age > 14 && $age <= 18) {
            return '14-18';
        } else {
            return '18+';
        }
    }

    /**
     * علاقات التكامل مع Moodle
     * اذا كنت تستخدم جدول moodle_enrollments:
     */
    public function moodleEnrollments()
    {
        // نفترض أن جدول moodle_enrollments فيه حقل user_id
        return $this->hasMany(MoodleEnrollment::class, 'user_id');
    }

    public function moodleGrades()
    {
        // نفترض أن جدول moodle_grades فيه حقل user_id
        return $this->hasMany(MoodleGrade::class, 'user_id');
    }

    /**
     *  Events Hooks للمزامنة مع Moodle
     */
    protected static function boot()
    {
        parent::boot();

        // تحديث المستخدم في Moodle عند تغيّر الاسم أو الإيميل (مثلاً)
        static::updated(function ($user) {
            if ($user->isDirty(['first_name', 'last_name', 'email'])) {
                event(new UserUpdated($user));
            }

            // تحديث كلمة المرور في Moodle عند تغييرها في Laravel
            if ($user->isDirty('password') && $user->moodle_id) {
                $moodleService = app(MoodleUserService::class);
                $moodleService->updatePassword($user, $user->password);
            }
        });

        // حذف المستخدم من Moodle عند حذفه من النظام
        static::deleting(function ($user) {
            if ($user->moodle_id) {
                $moodleService = app(MoodleUserService::class);
                $deleted = $moodleService->deleteUser($user->moodle_id);

                if ($deleted) {
                    Log::info("✅ تم حذف المستخدم {$user->email} من Moodle بنجاح.");
                } else {
                    Log::warning("⚠️ فشل حذف المستخدم {$user->email} من Moodle.");
                }
            }
        });
    }
}
