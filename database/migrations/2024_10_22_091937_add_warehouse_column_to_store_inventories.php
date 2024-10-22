<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseColumnToStoreInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_inventories', function (Blueprint $table) {
            $table->string('from_warehouse', 5)->nullable()->after('product_quality');
            $table->string('to_warehouse', 5)->nullable()->after('from_warehouse');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_inventories', function (Blueprint $table) {
            $table->dropColumn('from_warehouse');
            $table->dropColumn('to_warehouse');
        });
    }
}
