<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoodleGradesTable extends Migration
{
    public function up()
    {
        Schema::create('moodle_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id'); // يشير للـQuiz في نظامك
            $table->integer('grade')->nullable();
            $table->timestamps();

            // Foreign Keys — اختياري
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('moodle_grades');
    }
}
