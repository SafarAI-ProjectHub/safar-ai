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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('moodle_course_id')->index();
            $table->timestamp('enrolled_at')->nullable(); // وقت التسجيل
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moodle_enrollments');
    }
}
