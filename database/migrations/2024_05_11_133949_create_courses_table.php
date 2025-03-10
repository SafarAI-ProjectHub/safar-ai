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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->integer('level')->nullable();
            $table->enum('type', ['weekly', 'intensive'])->nullable();
            $table->boolean('completed')->default(false);
            $table->boolean('visibility')->default(true); // هل الكورس مرئي للطلاب؟
            $table->timestamp('startdate')->nullable(); // تاريخ بدء الدورة
            $table->timestamp('enddate')->nullable(); // تاريخ انتهاء الدورة
            $table->bigInteger('moodle_course_id')->nullable(); // ربط الدورة بـ Moodle
            $table->bigInteger('moodle_category_id')->nullable(); // ربط التصنيف بـ Moodle
            $table->string('moodle_enrollment_method')->nullable(); // طريقة التسجيل في Moodle
            $table->string('image')->nullable();
            $table->bigInteger('teacher_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    } 
};
