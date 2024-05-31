<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelTestAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_test_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_test_question_id');
            $table->unsignedBigInteger('user_id');
            $table->text('response');
            $table->boolean('correct');
            $table->text('ai_review')->nullable();
            $table->text('Admin_review')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('level_test_assessments');
    }
}