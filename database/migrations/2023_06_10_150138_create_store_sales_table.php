<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sales', function (Blueprint $table) {
            $table->id();
            $table->string('batch_date',15)->nullable();
            $table->string('batch_number',50)->nullable();
            $table->tinyInteger('is_valid')->length(3)->unsigned()->default(0);
            $table->tinyInteger('is_final')->length(3)->unsigned()->default(0);
            $table->tinyInteger('rr_flag')->length(10)->unsigned()->default(0);
            $table->tinyInteger('rr_flag_temp')->length(10)->unsigned()->nullable();
            $table->string('reference_number',10)->nullable();
            $table->integer('systems_id')->length(10)->unsigned()->nullable();
            $table->integer('organizations_id')->length(10)->unsigned()->nullable();
            $table->integer('report_types_id')->length(10)->unsigned()->nullable();
            $table->integer('channels_id')->length(10)->unsigned()->nullable();
            $table->integer('sales_transaction_types_id')->length(10)->unsigned()->nullable();
            $table->integer('employees_id')->length(10)->unsigned()->nullable();
            $table->integer('customers_id')->length(10)->unsigned()->nullable();
            $table->string('customer_location',100)->nullable();
            $table->string('receipt_number',100)->nullable();
            $table->date('sales_date')->nullable();
            $table->string('apple_yr_qtr_wk',100)->nullable();
            $table->string('apple_week_cutoff',100)->nullable();
            $table->string('non_apple_yr_mon_wk',100)->nullable();
            $table->string('non_apple_week_cutoff',100)->nullable();
            $table->string('sales_date_yr_mo',10)->nullable();
            $table->string('item_code',100)->nullable();
            $table->string('digits_code_rr_ref',50)->nullable();
            $table->string('digits_code',60)->nullable();
            $table->string('item_description',100)->nullable();
            $table->integer('quantity_sold')->length(10)->nullable();
            $table->double('sold_price', 16, 2)->nullable();
            $table->double('qtysold_price', 16, 2)->nullable();
            $table->double('store_cost', 16, 2)->nullable();
            $table->double('qtysold_sc', 16, 2)->nullable();
            $table->double('net_sales', 16, 2)->nullable();
            $table->string('sale_memo_reference',100)->nullable();
            $table->double('current_srp', 16, 2)->nullable();
            $table->double('qtysold_csrp', 16, 2)->nullable();
            $table->double('dtp_rf', 16, 2)->nullable();
            $table->double('qtysold_rf', 16, 2)->nullable();
            $table->double('landed_cost', 16, 2)->nullable();
            $table->double('qtysold_lc', 16, 2)->nullable();
            $table->double('dtp_ecom', 16, 2)->nullable();
            $table->double('qtysold_ecom', 16, 2)->nullable();
            $table->integer('created_by')->length(10)->unsigned()->nullable();
            $table->integer('updated_by')->length(10)->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('digits_code');
            $table->index('sales_date');
            $table->index('customers_id');
            $table->index('employees_id');
            $table->index(['batch_number', 'reference_number']);
            $table->index('systems_id');
            $table->index('organizations_id');
            $table->index('report_types_id');
            $table->index('channels_id');
            $table->index('digits_code_rr_ref');
            $table->index('customer_location');
            $table->index('sales_transaction_types_id');
            $table->index('item_code');
            $table->index('is_final');
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sales');
    }
}
