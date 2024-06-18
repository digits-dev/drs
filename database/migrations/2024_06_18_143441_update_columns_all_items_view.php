<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsAllItemsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS all_items;");
        DB::statement("
            CREATE VIEW all_items AS
            SELECT
               'items' as table_name,
                items.sku_status_description as status,
                items.digits_code,
                items.digits_code as item_code,
                items.item_description,
                items.current_srp,
                items.upc_code,
                items.upc_code2,
                items.upc_code3,
                items.upc_code4,
                items.upc_code5,
                items.brand_description,
                items.category_description,
                items.margin_category_description,
                items.vendor_type_code,
                items.inventory_type_description,
                items.sku_status_description,
                items.brand_status
            FROM items where sku_status_description != 'INVALID'
            UNION
            SELECT
                'admin_items' as table_name,
                admin_items.status,
                null as digits_code,
                admin_items.item_code,
                admin_items.item_description,
                admin_items.current_srp,
                admin_items.upc_code,
                admin_items.upc_code2,
                admin_items.upc_code3,
                admin_items.upc_code4,
                admin_items.upc_code5,
                admin_items.brand_description,
                admin_items.category_description,
                admin_items.margin_category_description,
                admin_items.vendor_type_code,
                admin_items.inventory_type_description,
                admin_items.sku_status_description,
                admin_items.brand_status
            FROM admin_items where status != 'INACTIVE'
            UNION
            SELECT
                'gacha_items' as table_name,
                gacha_items.status,
                gacha_items.digits_code as digits_code,
                gacha_items.digits_code as item_code,
                gacha_items.item_description,
                gacha_items.current_srp,
                gacha_items.upc_code,
                gacha_items.upc_code2,
                gacha_items.upc_code3,
                gacha_items.upc_code4,
                gacha_items.upc_code5,
                gacha_items.brand_description,
                gacha_items.category_description,
                gacha_items.margin_category_description,
                gacha_items.vendor_type_code,
                gacha_items.inventory_type_description,
                gacha_items.sku_status_description,
                gacha_items.brand_status
            FROM gacha_items where status != 'INACTIVE'
            UNION
            SELECT
                'rma_items' as table_name,
                rma_items.status,
                null as digits_code,
                rma_items.digits_code as item_code,
                rma_items.item_description,
                rma_items.current_srp,
                rma_items.upc_code,
                rma_items.upc_code2,
                rma_items.upc_code3,
                rma_items.upc_code4,
                rma_items.upc_code5,
                rma_items.brand_description,
                rma_items.category_description,
                rma_items.margin_category_description,
                rma_items.vendor_type_code,
                rma_items.inventory_type_description,
                rma_items.sku_status_description,
                rma_items.brand_status
            FROM rma_items
            UNION
            SELECT
                'service_items' as table_name,
                service_items.status,
                null as digits_code,
                service_items.item_code,
                service_items.item_description,
                service_items.current_srp,
                service_items.upc_code,
                service_items.upc_code2,
                service_items.upc_code3,
                service_items.upc_code4,
                service_items.upc_code5,
                service_items.brand_description,
                service_items.category_description,
                service_items.margin_category_description,
                service_items.vendor_type_code,
                service_items.inventory_type_description,
                service_items.sku_status_description,
                service_items.brand_status
            FROM service_items where status != 'INACTIVE';
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
