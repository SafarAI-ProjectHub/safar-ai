<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAmountColumnInPaymentsTable extends Migration
{
    /**
     * تشغيل التعديل.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
          
            $table->string('amount', 255)->change();
        });
    }

    /**
     * التراجع عن التعديل (عند rollback).
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
        });
    }
}
