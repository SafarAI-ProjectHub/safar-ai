<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['video', 'voice', 'pdf', 'image']);
            $table->string('link');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->dateTime('upload_date');
            $table->string('file_name');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->enum('status', ['processing', 'available', 'failed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
