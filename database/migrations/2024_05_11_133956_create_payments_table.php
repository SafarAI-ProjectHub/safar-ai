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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id');
            $table->string('paypal_subscription_id')->nullable();
            $table->foreignId('user_subscription_id');
            $table->foreignId('user_id');
            $table->decimal('amount', 8, 2);
            $table->enum('payment_status', [
                'pending',
                'completed',
                'failed',
                'denied',
                'refunded',
                'reversed',
                'refund_pending',
                'rejected',
            ])->default('pending');
            $table->enum('payment_type', ['paypal', 'cliq']);
            $table->string('paypal_payment_id')->nullable();
            $table->dateTime('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};