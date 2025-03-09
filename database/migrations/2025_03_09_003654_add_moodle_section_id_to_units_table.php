<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoodleSectionIdToUnitsTable extends Migration
{
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_section_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('moodle_section_id');
        });
    }
}
