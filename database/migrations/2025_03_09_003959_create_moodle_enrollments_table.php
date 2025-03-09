<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoodleEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::create('moodle_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('moodle_course_id');
            $table->timestamp('enrolled_at')->nullable(); // وقت التسجيل إن أردت
            $table->timestamps();

            // العلاقات (Foreign Keys) — اختياري إذا رغبت
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // يمكنك ترك المoodle_course_id دون FK، أو جعله يشير لـcourses->id
            // لو كانت تساوي معرف الدورة لدينا. لكن إن أردت ربطه بـmoodle_course_id
            // فقد لا يكون المفتاح الأساسي موجود في جدول courses.
        });
    }

    public function down()
    {
        Schema::dropIfExists('moodle_enrollments');
    }
}
