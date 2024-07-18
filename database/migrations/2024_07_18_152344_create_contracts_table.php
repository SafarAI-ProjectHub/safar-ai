<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->string('company_name')->default('Safar AI');
            $table->string('other_party_name');
            $table->date('contract_date');
            $table->string('company_logo')->default('assets/img/logo2.png');
            $table->decimal('salary', 10, 2);
            $table->text('signature')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Completed'])->default('Pending');
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}