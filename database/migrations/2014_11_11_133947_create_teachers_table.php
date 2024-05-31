<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('cv_link')->nullable();
            $table->integer('years_of_experience');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->defaulte('pending');
            $table->float('exam_score')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
