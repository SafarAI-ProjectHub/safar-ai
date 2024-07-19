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
            $table->string('other_party_name');
            $table->date('contract_date')->nullable();
            $table->decimal('salary', 10, 2);
            $table->enum('salary_period', ['hour', 'week', 'month']);
            $table->longText('contract_agreement');
            $table->longText('employee_duties');
            $table->longText('responsibilities');
            $table->longText('employment_period');
            $table->longText('compensation');
            $table->longText('legal_terms');
            $table->text('signature')->nullable();
            $table->enum('status', ['Pending', 'Approved'])->default('Pending');
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}