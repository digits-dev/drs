<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToStoreSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales', function (Blueprint $table) {
            $table->string('item_serial')->nullable()->after('qtysold_ecom');
            $table->string('sales_person')->nullable()->after('item_serial');
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
            $table->dropColumn('item_serial');
            $table->dropColumn('sales_person');
        });
    }
}
