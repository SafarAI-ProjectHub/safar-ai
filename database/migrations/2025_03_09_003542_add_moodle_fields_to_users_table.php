<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoodleFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_user_id')->nullable()->after('id');
            $table->unsignedBigInteger('moodle_role_id')->nullable()->after('moodle_user_id');
            $table->string('moodle_password')->nullable()->after('moodle_role_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['moodle_user_id', 'moodle_role_id', 'moodle_password']);
        });
    }
}
