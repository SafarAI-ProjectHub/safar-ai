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
        Schema::table('subscriptions', function (Blueprint $table) {
            // إذا أردت جعله VARCHAR
            $table->string('subscription_type', 20)->change();
            
            // أو إذا كنت تستخدم enum ويمكنك استخدام حزم خارجية لدعم تغيير enum في Laravel
            // $table->enum('subscription_type', ['monthly','yearly','yolo','solo','tolo'])->change();
        });
    }
    
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // أعده كما كان مثلاً:
            $table->enum('subscription_type', ['monthly','yearly'])->change();
        });
    }
    
};
