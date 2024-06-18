<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAdminItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('admin_items', function (Blueprint $table) {
        //     $table->string('upc_code')->nullable()->after('item_code');
        //     $table->string('upc_code2')->nullable()->after('upc_code');
        //     $table->string('upc_code3')->nullable()->after('upc_code2');
        //     $table->string('upc_code4')->nullable()->after('upc_code3');
        //     $table->string('upc_code5')->nullable()->after('upc_code4');
        //     $table->string('brand_description')->nullable()->after('upc_code5');
        //     $table->string('category_description')->nullable()->after('brand_description');
        //     $table->string('margin_category_description')->nullable()->after('category_description');
        //     $table->string('vendor_type_code')->nullable()->after('margin_category_description');
        //     $table->string('inventory_type_description')->nullable()->after('vendor_type_code');
        //     $table->string('sku_status_description')->nullable()->after('inventory_type_description');
        //     $table->string('brand_status')->nullable()->after('sku_status_description');
        //     $table->date('initial_wrr_date')->nullable()->after('current_srp');
        // });

        Schema::table('rma_items', function (Blueprint $table) {
            $table->string('upc_code')->nullable()->after('digits_code');
            $table->string('upc_code2')->nullable()->after('upc_code');
            $table->string('upc_code3')->nullable()->after('upc_code2');
            $table->string('upc_code4')->nullable()->after('upc_code3');
            $table->string('upc_code5')->nullable()->after('upc_code4');
            $table->string('brand_description')->nullable()->after('upc_code5');
            $table->string('category_description')->nullable()->after('brand_description');
            $table->string('margin_category_description')->nullable()->after('category_description');
            $table->string('vendor_type_code')->nullable()->after('margin_category_description');
            $table->string('inventory_type_description')->nullable()->after('vendor_type_code');
            $table->string('sku_status_description')->nullable()->after('inventory_type_description');
            $table->string('brand_status')->nullable()->after('sku_status_description');
            $table->date('initial_wrr_date')->nullable()->after('current_srp');
        });

        // Schema::table('gacha_items', function (Blueprint $table) {
        //     $table->string('upc_code')->nullable()->after('digits_code');
        //     $table->string('upc_code2')->nullable()->after('upc_code');
        //     $table->string('upc_code3')->nullable()->after('upc_code2');
        //     $table->string('upc_code4')->nullable()->after('upc_code3');
        //     $table->string('upc_code5')->nullable()->after('upc_code4');
        //     $table->string('brand_description')->nullable()->after('upc_code5');
        //     $table->string('category_description')->nullable()->after('brand_description');
        //     $table->string('margin_category_description')->nullable()->after('category_description');
        //     $table->string('vendor_type_code')->nullable()->after('margin_category_description');
        //     $table->string('inventory_type_description')->nullable()->after('vendor_type_code');
        //     $table->string('sku_status_description')->nullable()->after('inventory_type_description');
        //     $table->string('brand_status')->nullable()->after('sku_status_description');
        //     $table->date('initial_wrr_date')->nullable()->after('current_srp');
        // });

        Schema::table('service_items', function (Blueprint $table) {
            $table->string('upc_code')->nullable()->after('item_code');
            $table->string('upc_code2')->nullable()->after('upc_code');
            $table->string('upc_code3')->nullable()->after('upc_code2');
            $table->string('upc_code4')->nullable()->after('upc_code3');
            $table->string('upc_code5')->nullable()->after('upc_code4');
            $table->string('brand_description')->nullable()->after('upc_code5');
            $table->string('category_description')->nullable()->after('brand_description');
            $table->string('margin_category_description')->nullable()->after('category_description');
            $table->string('vendor_type_code')->nullable()->after('margin_category_description');
            $table->string('inventory_type_description')->nullable()->after('vendor_type_code');
            $table->string('sku_status_description')->nullable()->after('inventory_type_description');
            $table->string('brand_status')->nullable()->after('sku_status_description');
            $table->date('initial_wrr_date')->nullable()->after('current_srp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('admin_items', function (Blueprint $table) {
        //     $table->dropColumn('upc_code');
        //     $table->dropColumn('upc_code2');
        //     $table->dropColumn('upc_code3');
        //     $table->dropColumn('upc_code4');
        //     $table->dropColumn('upc_code5');
        //     $table->dropColumn('brand_description');
        //     $table->dropColumn('category_description');
        //     $table->dropColumn('margin_category_description');
        //     $table->dropColumn('vendor_type_code');
        //     $table->dropColumn('inventory_type_description');
        //     $table->dropColumn('sku_status_description');
        //     $table->dropColumn('brand_status');
        //     $table->dropColumn('initial_wrr_date');
        // });

        Schema::table('rma_items', function (Blueprint $table) {
            $table->dropColumn('upc_code');
            $table->dropColumn('upc_code2');
            $table->dropColumn('upc_code3');
            $table->dropColumn('upc_code4');
            $table->dropColumn('upc_code5');
            $table->dropColumn('brand_description');
            $table->dropColumn('category_description');
            $table->dropColumn('margin_category_description');
            $table->dropColumn('vendor_type_code');
            $table->dropColumn('inventory_type_description');
            $table->dropColumn('sku_status_description');
            $table->dropColumn('brand_status');
            $table->dropColumn('initial_wrr_date');
        });

        // Schema::table('gacha_items', function (Blueprint $table) {
        //     $table->dropColumn('upc_code');
        //     $table->dropColumn('upc_code2');
        //     $table->dropColumn('upc_code3');
        //     $table->dropColumn('upc_code4');
        //     $table->dropColumn('upc_code5');
        //     $table->dropColumn('brand_description');
        //     $table->dropColumn('category_description');
        //     $table->dropColumn('margin_category_description');
        //     $table->dropColumn('vendor_type_code');
        //     $table->dropColumn('inventory_type_description');
        //     $table->dropColumn('sku_status_description');
        //     $table->dropColumn('brand_status');
        //     $table->dropColumn('initial_wrr_date');
        // });

        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn('upc_code');
            $table->dropColumn('upc_code2');
            $table->dropColumn('upc_code3');
            $table->dropColumn('upc_code4');
            $table->dropColumn('upc_code5');
            $table->dropColumn('brand_description');
            $table->dropColumn('category_description');
            $table->dropColumn('margin_category_description');
            $table->dropColumn('vendor_type_code');
            $table->dropColumn('inventory_type_description');
            $table->dropColumn('sku_status_description');
            $table->dropColumn('brand_status');
            $table->dropColumn('initial_wrr_date');
        });
    }
}
