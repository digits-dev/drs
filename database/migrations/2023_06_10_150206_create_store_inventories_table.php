<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('batch_date',15)->nullable();
            $table->string('batch_number',50)->nullable();
            $table->tinyInteger('is_valid')->length(3)->unsigned()->default(0);
            $table->tinyInteger('is_final')->length(3)->unsigned()->default(0);
            $table->string('reference_number',10)->nullable();
            $table->integer('systems_id')->length(10)->unsigned()->nullable();
            $table->integer('organizations_id')->length(10)->unsigned()->nullable();
            $table->integer('report_types_id')->length(10)->unsigned()->nullable();
            $table->integer('channels_id')->length(10)->unsigned()->nullable();
            $table->integer('inventory_transaction_types_id')->length(10)->unsigned()->nullable();
            $table->integer('employees_id')->length(10)->unsigned()->nullable();
            $table->integer('customers_id')->length(10)->unsigned()->nullable();
            $table->string('customer_location',100)->nullable();
            $table->integer('concepts_id')->length(10)->unsigned()->nullable();
            $table->date('inventory_date')->nullable();
            $table->string('item_code',100)->nullable();
            $table->string('digits_code',60)->nullable();
            $table->integer('quantity_inv')->length(10)->nullable();
            $table->double('store_cost', 16, 2)->nullable();
            $table->double('qtyinv_sc', 16, 2)->nullable();
            $table->double('srp', 16, 2)->nullable();
            $table->double('qtyinv_srp', 16, 2)->nullable();
            $table->double('dtp_rf', 16, 2)->nullable();
            $table->double('qtyinv_rf', 16, 2)->nullable();
            $table->double('landed_cost', 16, 2)->nullable();
            $table->double('qtyinv_lc', 16, 2)->nullable();
            $table->double('dtp_ecom', 16, 2)->nullable();
            $table->double('qtyinv_ecom', 16, 2)->nullable();
            $table->integer('created_by')->length(10)->unsigned()->nullable();
            $table->integer('updated_by')->length(10)->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('digits_code');
            $table->index('inventory_date');
            $table->index('customers_id');
            $table->index('employees_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_inventories');
    }
}
