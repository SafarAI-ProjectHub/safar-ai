<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubeVideosTable extends Migration
{
    public function up()
    {
        Schema::create('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->string('video_id');
            $table->text('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->string('age_group');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('youtube_videos');
    }
}