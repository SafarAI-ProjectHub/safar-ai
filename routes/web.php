<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\levelTest\TeacherTestController;
use App\Http\Controllers\levelTest\StudentTestController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified', 'role:Admin|Super Admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// Allowed for Admin and Super Admin

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
    Route::post('courses/store', [AdminController::class, 'storeCourse'])->name('admin.storeCourse');
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
});

// Routes for Super Admin, Admin, and Teacher
Route::middleware(['auth', 'role:Super Admin|Admin|Teacher'])->group(function () {
    // Courses
    Route::get('courses', [AdminController::class, 'courses'])->name('admin.courses');

    Route::get('courses/data', [AdminController::class, 'getCourses'])->name('admin.getCourses');

    // Units
    Route::get('courses/{courseId}/units', [AdminController::class, 'showUnits'])->name('admin.showUnits');
    Route::get('courses/{courseId}/units/data', [AdminController::class, 'getUnits'])->name('admin.getUnits');
    Route::post('units/store', [AdminController::class, 'storeUnit'])->name('admin.storeUnit');
    Route::get('units/{id}/edit', [AdminController::class, 'editUnit'])->name('admin.units.edit');
    Route::put('units/{id}', [AdminController::class, 'updateUnit'])->name('admin.units.update');
    Route::delete('courses/units/delete/{id}', [AdminController::class, 'destroyUnit'])->name('units.destroy');
    Route::get('courses/getUnits/{courseId}', [QuizController::class, 'getUnits'])->name('quiz.getUnits');

    // Quizzes
    Route::post('quizzes/store', [QuizController::class, 'storeQuiz'])->name('quiz.storeQuiz');
    Route::get('courses/quiz', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/datatable', [QuizController::class, 'dataTable'])->name('quizzes.datatable');
    Route::get('quizzes/{quizId}/edit', [QuizController::class, 'editQuiz'])->name('quiz.editQuiz');
    Route::put('quizzes/{quizId}/update', [QuizController::class, 'updateQuiz'])->name('quiz.updateQuiz');
    Route::delete('quizzes/{quizId}/delete', [QuizController::class, 'deleteQuiz'])->name('quiz.deleteQuiz');
    Route::get('courses/quiz/add', [QuizController::class, 'addQuizPage'])->name('quiz.addPage');
    Route::get('courses/quiz/edit', [QuizController::class, 'editQuizPage'])->name('quiz.editPage');

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
    Route::get('dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('courses', [TeacherController::class, 'getCourses'])->name('teacher.courses');
    Route::get('quiz-results/{courseId}', [TeacherController::class, 'getStudentQuizResults'])->name('teacher.quizResults');
    Route::get('student-profiles', [TeacherController::class, 'getStudentProfiles'])->name('teacher.getStudentProfiles');
    Route::get('student-profiles/{id}', [TeacherController::class, 'showStudentProfile'])->name('teacher.showStudentProfile');

});

// Routes for Admin access only
Route::middleware(['auth', 'role:Admin'])->get('/admin', [AdminController::class, 'index'])->name('admin.uploadVideo');

// Routes for Teacher access only
Route::middleware(['auth', 'role:Teacher'])->get('/teacher', [TeacherController::class, 'index'])->name('teacher.dashboard');

// Routes for Student access only
Route::middleware(['auth', 'role:Student'])->get('/student', [StudentController::class, 'index'])->name('student.dashboard');

// groupe for all Auth users
Route::middleware(['auth'])->group(function () {
    // courses
    Route::get('courses/{courseId}/show', [CourseController::class, 'showcourse'])->name('admin.showcourse');
});