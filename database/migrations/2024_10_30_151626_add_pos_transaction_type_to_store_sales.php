<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosTransactionTypeToStoreSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales', function (Blueprint $table) {
            $table->string('pos_transaction_type')->nullable()->after('sales_person');
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
            $table->dropColumn('pos_transaction_type');
        });
    }
}
