<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToStoreInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_inventories', function (Blueprint $table) {
            $table->string('product_quality')->nullable()->after('qtyinv_ecom');
        });

        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->string('product_quality')->nullable()->after('qtyinv_ecom');
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
            $table->dropColumn('product_quality');
        });

        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->dropColumn('product_quality');
        });
    }
}
