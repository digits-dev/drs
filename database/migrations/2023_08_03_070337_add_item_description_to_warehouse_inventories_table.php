<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemDescriptionToWarehouseInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->string('item_description',150)->nullable()->after('digits_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->dropColumn('item_description');
        });
    }
}
