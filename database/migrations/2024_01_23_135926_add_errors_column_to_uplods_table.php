<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddErrorsColumnToUplodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('headings');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('headings');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('headings');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('headings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->dropColumn('errors');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->dropColumn('errors');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('errors');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('errors');
        });
    }
}
