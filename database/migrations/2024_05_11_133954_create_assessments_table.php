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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('attempt_number')->default(1);
            $table->decimal('total_score', 5, 2);
            $table->float('ai_mark')->nullable();
            $table->float('teacher_mark')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->text('ai_notes')->nullable();
            $table->boolean('ai_assessment');
            $table->boolean('teacher_review');
            $table->dateTime('assessment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};