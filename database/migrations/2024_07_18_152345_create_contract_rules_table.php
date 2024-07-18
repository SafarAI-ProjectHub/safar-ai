<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractRulesTable extends Migration
{
    public function up()
    {
        Schema::create('contract_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->text('rule');
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_rules');
    }
}