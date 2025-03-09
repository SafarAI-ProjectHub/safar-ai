<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->string('question_text');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'long_answer', 'matching']); // دعم كل الأنواع
            $table->integer('order')->default(1); 
            $table->decimal('score', 5, 2)->default(1.0); 
            $table->bigInteger('moodle_question_id')->nullable();
            $table->foreignId('media_url')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
