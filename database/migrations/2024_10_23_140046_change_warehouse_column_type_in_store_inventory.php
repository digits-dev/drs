<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWarehouseColumnTypeInStoreInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_inventories', function (Blueprint $table) {
            $table->string('from_warehouse', 100)->change();
            $table->string('to_warehouse', 100)->change();
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
            $table->string('from_warehouse', 5)->change();
            $table->string('to_warehouse', 5)->change();
        });
    }
}
