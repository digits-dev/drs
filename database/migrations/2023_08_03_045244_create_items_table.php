<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('digits_code',100);
            $table->string('upc_code',100)->nullable();
            $table->string('upc_code2',100)->nullable();
            $table->string('upc_code3',100)->nullable();
            $table->string('upc_code4',100)->nullable();
            $table->string('upc_code5',100)->nullable();
            $table->string('item_description',100);
            $table->string('brand_description',100);
            $table->string('category_description',100);
            $table->string('margin_category_description',100);
            $table->string('vendor_type_code',30);
            $table->string('inventory_type_description',30);
            $table->string('sku_status_description',30);
            $table->string('brand_status',30);
            $table->decimal('current_srp', 16, 2)->nullable();


            $table->index('digits_code');
            $table->index('brand_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
