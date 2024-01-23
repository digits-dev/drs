<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromDateAndToDateColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('batch');
            $table->date('to_date')->nullable()->after('from_date');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('batch');
            $table->date('to_date')->nullable()->after('from_date');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('batch');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('batch');
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
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('from_date');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('from_date');
        });
    }
}
