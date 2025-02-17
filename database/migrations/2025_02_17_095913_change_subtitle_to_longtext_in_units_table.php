<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            // غيّر نوع الحقل subtitle إلى longText
            $table->longText('subtitle')->nullable()->change();
        });
    }

    public function down()
    {
        // لو أردت عكس العملية في حالة rollback
        Schema::table('units', function (Blueprint $table) {
            $table->string('subtitle', 255)->nullable()->change();
        });
    }
};
