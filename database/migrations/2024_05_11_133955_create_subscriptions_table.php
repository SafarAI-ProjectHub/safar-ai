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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->enum('payment_method', ['card', 'cliq', 'paypal']);
            $table->enum('subscription_type', ['monthly', 'yearly', 'trial']);
            $table->string('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('paypal_plan_id')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};