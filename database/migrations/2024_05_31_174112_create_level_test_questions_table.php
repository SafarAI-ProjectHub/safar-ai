<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_test_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_test_id');
            $table->text('question_text');
            $table->text('sub_text')->nullable();
            $table->enum('question_type', ['text', 'choice', 'voice', 'video']);
            $table->unsignedInteger('mark')->nullable();
            $table->string('media_url')->nullable();
            $table->enum('media_type', ['image', 'video', 'audio'])->nullable();
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
        Schema::dropIfExists('level_test_questions');
    }
}