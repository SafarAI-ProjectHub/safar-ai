<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('blocks', function (Blueprint $table) {
        $table->id(); // `id` كـ Primary Key
        $table->string('name'); // اسم البلوك
        $table->text('description')->nullable(); // وصف البلوك (اختياري)
        $table->timestamps(); // `created_at` و `updated_at`
    });
}

public function down()
{
    Schema::dropIfExists('blocks');
}

};
