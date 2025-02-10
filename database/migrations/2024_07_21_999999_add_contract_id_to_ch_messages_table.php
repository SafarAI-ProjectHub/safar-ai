<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractIdToChMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('ch_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable()->after('id');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ch_messages', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
    }
}