<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoomMeetingsTable extends Migration
{
    public function up()
    {
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('meeting_id');
            $table->string('topic');
            $table->text('agenda')->nullable();
            $table->dateTime('start_time');
            $table->integer('duration')->comment('Duration in minutes');
            $table->string('password')->nullable();
            $table->string('join_url');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zoom_meetings');
    }
}