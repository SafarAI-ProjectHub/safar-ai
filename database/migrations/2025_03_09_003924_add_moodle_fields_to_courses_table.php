<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoodleFieldsToCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_course_id')->nullable()->after('id');
            $table->unsignedBigInteger('moodle_category_id')->nullable()->after('moodle_course_id');
            $table->string('moodle_enrollment_method')->nullable()->after('moodle_category_id');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['moodle_course_id', 'moodle_category_id', 'moodle_enrollment_method']);
        });
    }
}
