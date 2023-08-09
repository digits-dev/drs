<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transaction_types', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_transaction_type',150)->nullable();
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
        Schema::dropIfExists('inventory_transaction_types');
    }
}
