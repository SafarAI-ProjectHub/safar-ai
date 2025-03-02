<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\QuizResultController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CliqController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminBillingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\levelTest\TeacherTestController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\levelTest\StudentTestController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Vendor\Chatify\MessagesController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\YoutubeVideoController;



Broadcast::routes(['middleware' => ['auth']]);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/', [HomeController::class, 'index']);
Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');

// PayPal webhook
Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handleWebhook'])->name('paypal.webhook');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:Admin|Super Admin'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// contact us
Route::post('/forms/contact', [ContactController::class, 'submit']);
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin')->group(function () {

    Route::get('list', [AdminController::class, 'listAdmin'])->name('admin.list');
    Route::get('create', [AdminController::class, 'createAdmin'])->name('admin.create');
    Route::post('store', [AdminController::class, 'storeAdmin'])->name('admin.store');
    Route::get('edit/{id}', [AdminController::class, 'editAdmin'])->name('admin.edit');
    Route::put('update/{id}', [AdminController::class, 'updateAdmin'])->name('admin.update');
    Route::delete('delete/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.delete');

});

// Routes for Admin and Super Admin only
Route::middleware(['auth', 'role:Admin|Super Admin'])->prefix('admin')->group(function () {
    // Teachers Applications
    Route::get('applications', [AdminController::class, 'applicationsIndex'])->name('admin.applications');
    Route::get('applications/data', [AdminController::class, 'getApplicationsIndex'])->name('admin.getApplicationsIndex');
    Route::post('update-teacher-status', [AdminController::class, 'updateTeacherStatus'])->name('admin.updateTeacherStatus');

    // Teachers
    Route::get('teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::get('teachers/data', [AdminController::class, 'getTeachers'])->name('admin.getteachers');
    Route::get('teachers/{id}/edit', [AdminController::class, 'editTeacher'])->name('admin.editTeacher');
    Route::put('teachers/{id}', [AdminController::class, 'updateTeacher'])->name('admin.updateTeacher');
    Route::delete('teachers/{id}', [AdminController::class, 'deleteTeacher'])->name('admin.deleteTeacher');
    // Students
    Route::get('/students', [AdminController::class, 'showStudents'])->name('admin.students');
    Route::get('students/data', [AdminController::class, 'getStudents'])->name('admin.students.data');
    Route::get('student/{id}/edit', [AdminController::class, 'editStudent'])->name('admin.student.edit');
    Route::put('student/{id}/update', [AdminController::class, 'updateStudent'])->name('admin.student.update');
    Route::delete('student/{id}/delete', [AdminController::class, 'deleteStudent'])->name('admin.student.delete');

    // Courses
    Route::get('/teachers/for-assignment', [AdminController::class, 'getTeachersForAssignment'])->name('admin.getTeachersForAssignment');
    Route::post('/courses/assign-teacher', [AdminController::class, 'assignTeacherToCourse'])->name('admin.assignTeacherToCourse');

    // Quizzes
    // Route::get('courses/quiz/add', [QuizController::class, 'addQuizPage'])->name('quiz.addPage');
    // Route::get('courses/quiz', [QuizController::class, 'index'])->name('quizzes.index');
    // Route::get('courses/quiz/edit', [QuizController::class, 'editQuizPage'])->name('quiz.editPage');
    // Route::get('quizzes/datatable', [QuizController::class, 'dataTable'])->name('quizzes.datatable');
    // Route::post('quizzes/store', [QuizController::class, 'storeQuiz'])->name('quiz.storeQuiz');
    // Route::get('quizzes/{quizId}/edit', [QuizController::class, 'editQuiz'])->name('quiz.editQuiz');
    // Route::put('quizzes/{quizId}/update', [QuizController::class, 'updateQuiz'])->name('quiz.updateQuiz');
    // Route::delete('quizzes/{quizId}/delete', [QuizController::class, 'deleteQuiz'])->name('quiz.deleteQuiz');

    // Teacher Level Tests
    Route::get('level-test/teacher/add', [TeacherTestController::class, 'addTestPage'])->name('teacherTest.addPage');
    Route::get('level-test-teacher', [TeacherTestController::class, 'index'])->name('teacherTests.index');
    Route::get('teacher/{testId}/edit', [TeacherTestController::class, 'editTest'])->name('teacherTest.editPage');
    Route::post('teacher/store', [TeacherTestController::class, 'storeTest'])->name('teacherTest.store');
    Route::get('teacher/datatable', [TeacherTestController::class, 'dataTable'])->name('teacherTests.datatable');
    Route::put('teacher/{testId}/update', [TeacherTestController::class, 'updateTest'])->name('teacherTest.update');
    Route::delete('teacher/{testId}/delete', [TeacherTestController::class, 'deleteTest'])->name('teacherTest.delete');
    Route::post('teacher/{testId}/activate', [TeacherTestController::class, 'activateTest'])->name('teacherTest.activate');

    // Student Level Tests
    Route::get('level-test/student/add', [StudentTestController::class, 'addTestPage'])->name('studentTest.addPage');
    Route::get('level-test-student', [StudentTestController::class, 'index'])->name('studentTests.index');
    Route::get('student/test/{testId}/edit', [StudentTestController::class, 'editTest'])->name('studentTest.editPage');
    Route::post('student/test/store', [StudentTestController::class, 'storeTest'])->name('studentTest.store');
    Route::get('studen/tests/datatable', [StudentTestController::class, 'dataTable'])->name('studentTests.datatable');
    Route::put('student/test/{testId}/update', [StudentTestController::class, 'updateTest'])->name('studentTest.update');
    Route::delete('student/test/{testId}/delete', [StudentTestController::class, 'deleteTest'])->name('studentTest.delete');
    Route::post('student/test/{testId}/activate', [StudentTestController::class, 'activateTest'])->name('studentTest.activate');

    // File Uploads
    Route::post('/upload', [FileUploadController::class, 'process'])->name('filepond.upload');
    Route::delete('/upload', [FileUploadController::class, 'revert'])->name('filepond.revert');
    Route::get('/upload/{id}', [FileUploadController::class, 'load'])->name('filepond.load');

    // Subscriptions
    Route::get('users/subscriptions', [AdminBillingController::class, 'subscriptions'])->name('admin.subscriptions');
    Route::get('inactive_subscriptions', [AdminBillingController::class, 'InactiveSubscriptions'])->name('admin.inactive_subscriptions');
    Route::get('payments', [AdminBillingController::class, 'payments'])->name('admin.payments');
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [AdminSubscriptionController::class, 'index'])->name('admin.subscriptions.index');
        Route::post('/', [AdminSubscriptionController::class, 'store'])->name('admin.subscriptions.store');
        Route::post('/toggle-active/{id}', [AdminSubscriptionController::class, 'toggleActive'])->name('admin.subscriptions.toggleActive');
    });

    // cliq payments
    Route::get('/pending-payments', [CliqController::class, 'showPendingPayments'])->name('showPendingPayments');
    Route::post('/approve-payment/{id}', [CliqController::class, 'approvePayment'])->name('admin.approvePayment');
    Route::post('/reject-payment/{id}', [CliqController::class, 'rejectPayment'])->name('admin.rejectPayment');

    //contact us form 
    Route::get('/contact-forms', [ContactController::class, 'index'])->name('admin.contact_forms.index');
    Route::get('/contact-forms/data', [ContactController::class, 'getContactForms'])->name('admin.contact_forms.data');
    Route::post('/contact-forms/{id}/resolved', [ContactController::class, 'markAsResolved'])->name('admin.contact_forms.resolved');
    Route::delete('/contact-forms/{id}', [ContactController::class, 'destroy'])->name('admin.contact_forms.destroy');

    // Teacher level test Assessment
    Route::get('/teachers/levelTestAssessment', [AdminController::class, 'teacherAssessments'])->name('admin.getTeachersAssessments');
    Route::get('/teachers/levelTest', [AdminController::class, 'getTeachersWithAssessments'])->name('admin.getTeachersWithAssessments');
    Route::get('/teachers/{id}/assessments', [AdminController::class, 'getTeacherAssessments']);
    Route::put('/teachers/{teacherId}/assessments/{assessmentId}', [AdminController::class, 'updateTeacherAssessment']);

    // Student level test Assessment
    Route::get('/students/levelTestAssessment', [AdminController::class, 'studentAssessments'])->name('admin.getStudentsAssessments');
    Route::get('/students/levelTest', [AdminController::class, 'getStudentsWithAssessments'])->name('admin.getStudentsWithAssessments');
    Route::get('/students/{id}/assessments', [AdminController::class, 'getStudentAssessments']);
    Route::put('/students/{studentId}/assessments/{assessmentId}', [AdminController::class, 'updateStudentAssessment']);

    // offers
    Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
    Route::post('offers', [OfferController::class, 'store'])->name('offers.store');
    Route::get('offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
    Route::post('offers/{offer}/update', [OfferController::class, 'update'])->name('offers.update');
    Route::delete('offers/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');
    Route::post('offers/{offer}/toggle', [OfferController::class, 'toggle'])->name('offers.toggle');

    // Rates/Reviews
    Route::get('/reviews', [RateController::class, 'index'])->name('admin.reviews.index');
    Route::delete('reviews/{rate}', [RateController::class, 'destroy'])->name('rates.destroy');

    // contract
    Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('contracts/{id}', [ContractController::class, 'show']);
    Route::get('contracts/create/{teacherId}', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('contracts/{contractId}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('contracts/{contractId}', [ContractController::class, 'update'])->name('contracts.update');

    //Tracing time 
    Route::get('/teachers/logs', [UserActivityController::class, 'getDailyActivity'])->name('teachers.logs.index');
    Route::get('/teacher/logs/{id}', [UserActivityController::class, 'showLogs'])->name('teacher.logs.show');
    Route::get('/teacher/{id}/monthly-activity', [UserActivityController::class, 'getMonthlyActivity'])->name('teacher.monthly.activity');

    // youtube videos 
    Route::get('youtube_videos', [YoutubeVideoController::class, 'index'])->name('youtube_videos.index');
    Route::post('youtube_videos', [YoutubeVideoController::class, 'store'])->name('youtube_videos.store');
    Route::get('youtube_videos/{id}', [YoutubeVideoController::class, 'show'])->name('youtube_videos.show');
    Route::put('youtube_videos/{id}', [YoutubeVideoController::class, 'update'])->name('youtube_videos.update');
    Route::delete('youtube_videos/{id}', [YoutubeVideoController::class, 'destroy'])->name('youtube_videos.destroy');

});

// Routes for Super Admin, Admin, and Teacher
Route::middleware(['auth', 'role:Super Admin|Admin|Teacher'])->group(function () {
    // Courses
    Route::get('courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::get('courses/data', [AdminController::class, 'getCourses'])->name('admin.getCourses');
    Route::post('/courses/{course}/toggle-complete', [AdminController::class, 'toggleComplete'])->name('courses.toggleComplete');
    Route::post('courses/store', [AdminController::class, 'storeCourse'])->name('admin.storeCourse');
    Route::delete('courses/delete/{id?}', [AdminController::class, 'deleteCourse'])->name('admin.courses.delete');


    // Units
    Route::get('courses/{courseId}/units', [AdminController::class, 'showUnits'])->name('admin.showUnits');
    // Route::get('courses/{courseId}/units/data', [AdminController::class, 'getUnits'])->name('admin.getUnits');
    Route::get('/admin/courses/{courseId}/units/data', [AdminController::class, 'getUnits'])
     ->name('admin.getUnits');
     // تعديل الوحدة (الدورة)
Route::get('admin/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('admin.courses.edit');
Route::post('admin/courses/{id}/update', [AdminController::class, 'updateCourse'])->name('admin.courses.update');

    Route::post('units/store', [AdminController::class, 'storeUnit'])->name('admin.storeUnit');
    Route::get('units/{id}/edit', [AdminController::class, 'editUnit'])->name('admin.units.edit');
    Route::put('units/{id}', [AdminController::class, 'updateUnit'])->name('admin.units.update');
    Route::delete('courses/units/delete/{id}', [AdminController::class, 'destroyUnit'])->name('units.destroy');
    Route::get('courses/getUnits/{courseId}', [QuizController::class, 'getUnits'])->name('quiz.getUnits');
    Route::get('/units/{id}/script', [AdminController::class, 'getScript'])->name('units.getScript');
    Route::post('/units/{id}/script', [AdminController::class, 'updateScript'])->name('units.updateScript');
    Route::get('/units/{id}', [AdminController::class, 'showUnit'])->name('units.show');
    Route::post('/upload-canvas-image', [FileUploadController::class, 'uploadCanvasImage']);

    // Quizzes
    Route::post('quizzes/store', [QuizController::class, 'storeQuiz'])->name('quiz.storeQuiz');
    Route::get('courses/quiz', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/datatable', [QuizController::class, 'dataTable'])->name('quizzes.datatable');
    Route::get('quizzes/{quizId}/edit', [QuizController::class, 'editQuiz'])->name('quiz.editQuiz');
    Route::put('quizzes/{quizId}/update', [QuizController::class, 'updateQuiz'])->name('quiz.updateQuiz');
    Route::delete('quizzes/{quizId}/delete', [QuizController::class, 'deleteQuiz'])->name('quiz.deleteQuiz');
    Route::get('courses/quiz/add', [QuizController::class, 'addQuizPage'])->name('quiz.addPage');
    Route::get('courses/quiz/edit', [QuizController::class, 'editQuizPage'])->name('quiz.editPage');
    Route::get('quizzes/{quizId}/results', [QuizController::class, 'showResults'])->name('quiz.showResults');
    Route::get('quizzes/{quizId}/results/data', [QuizController::class, 'resultsDataTable'])->name('quiz.resultsDataTable');
    Route::get('assessments/{assessmentId}/response', [QuizController::class, 'getStudentResponse'])->name('assessment.getStudentResponse');
    Route::post('assessments/{assessmentId}/review', [QuizController::class, 'saveReview'])->name('assessment.saveReview');

    //quiz results
    Route::get('/quiz-results', [QuizResultController::class, 'index'])->name('quizResults.index');
    Route::get('/quiz-results/{id}', [QuizResultController::class, 'show'])->name('quizResults.show');
    Route::post('/quiz-results/{id}/update', [QuizResultController::class, 'update'])->name('quizResults.update');
    Route::get('/units/byCourse', [QuizResultController::class, 'byCourse'])->name('units.byCourse');


    // Meeting Routes
    Route::get('zoom-meetings', [ZoomMeetingController::class, 'index'])->name('zoom-meetings.index');
    Route::get('zoom-meetings/datatable', [ZoomMeetingController::class, 'getMeetings'])->name('zoom-meetings.datatable');
    Route::get('zoom-meetings/create', [ZoomMeetingController::class, 'create'])->name('zoom-meetings.create');
    Route::post('zoom-meetings', [ZoomMeetingController::class, 'store'])->name('zoom-meetings.store');
    Route::get('zoom-meetings/{zoomMeeting}', [ZoomMeetingController::class, 'show'])->name('zoom-meetings.show');
    Route::get('zoom-meetings/{zoomMeeting}/edit', [ZoomMeetingController::class, 'edit'])->name('zoom-meetings.edit');
    Route::put('zoom-meetings/{zoomMeeting}', [ZoomMeetingController::class, 'update'])->name('zoom-meetings.update');
    Route::delete('zoom-meetings/{zoomMeeting}', [ZoomMeetingController::class, 'destroy'])->name('zoom-meetings.destroy');
});

// Routes for Teacher and Super Admin
Route::middleware(['auth', 'role:Teacher|Super Admin'])->prefix('teacher')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('courses', [TeacherController::class, 'getCourses'])->name('teacher.courses');
    Route::post('/level-test/submit', [TeacherController::class, 'submit'])->name('teacher.level-test.submit');
    Route::get('quiz-results/{courseId}', [TeacherController::class, 'getStudentQuizResults'])->name('teacher.quizResults');
    Route::get('student-profiles', [TeacherController::class, 'getStudentProfiles'])->name('teacher.getStudentProfiles');
    Route::get('student-profiles/{id}', [TeacherController::class, 'showStudentProfile'])->name('teacher.showStudentProfile');

    // My contract 

    Route::get('my-contract', [ContractController::class, 'myContract'])->name('contracts.myContract');
    Route::post('my-contract/sign', [ContractController::class, 'signContract'])->name('contracts.signContract');
    Route::get('/contracts/{contractId}/download-pdf', [ContractController::class, 'downloadContractPDF'])->name('contracts.downloadPDF');

});

// Routes for Students and Super Admin
Route::middleware(['auth', 'role:Student'])->prefix('student')->group(function () {
    // meetings
    Route::get('/meetings', [StudentController::class, 'myMeetings'])->name('student.meetings.index');
    Route::get('/meetings/datatable', [StudentController::class, 'getMeetings'])->name('student.meetings.datatable');
    Route::get('/meetings/{id}', [StudentController::class, 'showMeeting'])->name('student.meetings.show');

    // subscriptions
        // راوت للاشتراك المجاني
        Route::post('/subscribe-free', [SubscriptionController::class, 'subscribeFree'])
        ->name('subscription.subscribeFree');
        
    // Route::post('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::get('subscription/details', [SubscriptionController::class, 'showSubscriptionDetails'])->name('subscription.details');

    // unit progress
    

    Route::post('/course/update-unit-completion', [CourseController::class, 'updateUnitCompletion'])
        ->name('course.updateUnitCompletion');

    Route::post('/mark-lesson-completed', [CourseController::class, 'updateUnitCompletion'])
        ->name('student.markLessonCompleted');

    //Certificate
    Route::get('/certificate/check', [CertificateController::class, 'check'])->name('certificate.check');
    Route::get('/certificate/download', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/certificate/page/{course_id}', [CertificateController::class, 'certificatePage'])->name('certificate.review');
    Route::post('/certificate/download', [CertificateController::class, 'generatePDF'])->name('certificate.generatePDF');
    Route::get('/my-certificates', [CertificateController::class, 'myCertificates'])->name('student.myCertificates');
});


// Routes for Admin access only
Route::middleware(['auth', 'role:Admin'])->get('/admin', [AdminController::class, 'index'])->name('admin.uploadVideo');

// Routes for Student access only
Route::middleware(['auth', 'role:Student'])->group(function () {

    Route::get('/student', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/reactivate', [SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');

    // courses routes 
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('student.myCourses');
    Route::get('/course/details', [StudentController::class, 'getCourseDetails'])->name('student.getCourseDetails');
    Route::post('/course/enroll', [StudentController::class, 'enroll'])->name('student.enroll');

    // level test
    Route::get('/level-test', [StudentController::class, 'levelTest'])->name('level.test');

    // Cliq payments 
    Route::post('/pay-with-cliq', [CliqController::class, 'payWithCliq'])->name('payWithCliq');
    Route::post('/reupload-payment-proof/{id}', [CliqController::class, 'reuploadPaymentProof'])->name('reuploadPaymentProof');

    // Quiz 
    Route::get('/student/quizzes', [StudentQuizController::class, 'listQuizzes'])->name('student.quizzes.list');
    Route::get('/student/quizzes/{id}', [StudentQuizController::class, 'showQuiz'])->name('student.quiz.show');
    Route::post('/student/quizzes/{id}/submit', [StudentQuizController::class, 'submitQuiz'])->name('student.quiz.submit');
    Route::get('/quiz/{id}/result', [StudentQuizController::class, 'showQuizResult'])->name('student.quiz.result');

    Route::post('/rate-course', [CourseController::class, 'rateCourse'])->name('course.rate');
    //Rate Course
});


// groupe for all Auth users
Route::middleware(['auth'])->group(function () {
    // courses
    Route::get('courses/{courseId}/show', [CourseController::class, 'showcourse'])->name('admin.showcourse');

    // Notifications
    Route::get('/notifications/get', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/mark-as-seen', [NotificationController::class, 'markAsSeen'])->name('notifications.markAsSeen');

});


Route::middleware(['auth', 'role:Admin|Super Admin'])->group(function () {
    // Permissions
    Route::get('/manage-permissions', [PermissionController::class, 'index'])->name('manage.permissions');
    Route::post('/manage-permissions', [PermissionController::class, 'update'])->name('manage.permissions.update');
});

// routes/web.php

Route::get('/level-test', [StudentController::class, 'levelTest'])->name('student.level_test');
Route::post('/level-test/submit', [StudentController::class, 'submit'])->name('level-test.submit');
Route::get('/Home', [StudentController::class, 'index'])->name('student.level_test.home');


// webhooks
Route::get('/paypal/return', [SubscriptionController::class, 'handleReturn'])->name('paypal.return');
Route::get('/paypal/cancel', [SubscriptionController::class, 'handleCancel'])->name('paypal.cancel');


Route::middleware(['auth'])->group(function () {
    Route::post('/update-activity-status', [UserActivityController::class, 'updateStatus']);
});