<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoodleLogsTable extends Migration
{
    public function up()
    {
        Schema::create('moodle_logs', function (Blueprint $table) {
            $table->id();
            $table->string('operation')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moodle_logs');
    }
}
