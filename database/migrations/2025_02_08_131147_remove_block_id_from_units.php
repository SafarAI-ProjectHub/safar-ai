<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['block_id']); // إذا كان لديك مفتاح أجنبي
            $table->dropColumn('block_id'); // حذف العمود
        });
    }

    public function down()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable();
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
        });
    }
};
