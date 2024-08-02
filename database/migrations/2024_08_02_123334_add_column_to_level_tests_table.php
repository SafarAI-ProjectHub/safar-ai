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
        Schema::table('level_tests', function (Blueprint $table) {
            $table->unsignedBigInteger('age_group_id')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('level_tests', function (Blueprint $table) {
            $table->dropColumn('age_group_id');
        });
    }
};