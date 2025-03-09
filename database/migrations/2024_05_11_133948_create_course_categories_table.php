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
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->foreignId('parent_id')->nullable()->constrained('course_categories')->onDelete('cascade');    
            $table->bigInteger('moodle_category_id')->nullable();
            $table->enum('age_group', ['6-10', '10-14', '14-18', '18+'])->nullable();
            $table->enum('general_category', ['Mathematics', 'Science', 'Programming', 'Arts', 'Languages', 'Business'])->nullable();    
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_categories');
    }
};
