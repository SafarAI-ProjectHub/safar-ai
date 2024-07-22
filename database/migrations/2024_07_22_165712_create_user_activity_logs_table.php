<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('login_time')->nullable();
            $table->timestamp('logout_time')->nullable();
            $table->timestamp('last_activity_time')->nullable();
            $table->enum('session_status', ['active', 'inactive', 'ended'])->default('active');
            $table->integer('total_active_time')->default(0); // in seconds
            $table->timestamp('current_activity_start')->nullable();
            $table->integer('previous_activity_time')->default(0); // in seconds
            $table->timestamp('stop_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activity_logs');
    }
}