<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesYearAndMonthToStoreSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales', function (Blueprint $table) {
            $table->integer('sales_year')->length(10)->unsigned()->nullable()->after('sales_date_yr_mo');
            $table->integer('sales_month')->length(10)->unsigned()->nullable()->after('sales_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_sales', function (Blueprint $table) {
            //
        });
    }
}
