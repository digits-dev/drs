<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('channels_id')->length(10)->unsigned()->nullable();
            $table->string('trade_name',150)->nullable();
            $table->string('mall',150)->nullable();
            $table->string('branch',150)->nullable();
            $table->string('customer_bill_to',150)->nullable();
            $table->string('customer_name',150)->nullable();
            $table->integer('concepts_id')->length(10)->unsigned()->nullable();
            $table->string('status', 10)->default('ACTIVE')->nullable();
            $table->integer('created_by')->length(10)->unsigned()->nullable();
            $table->integer('updated_by')->length(10)->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
