<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGashaponInventoryReportView extends Migration
{
    private $view_name = 'gashapon_inventories_report';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS $this->view_name");
        DB::statement("
        CREATE VIEW $this->view_name AS
        SELECT
        `gashapon_inventories`.`id`,
        `gashapon_inventories`.`batch_number`,
        `gashapon_inventories`.`is_final`,
        `gashapon_inventories`.`reference_number`,
        `systems`.`system_name` AS `system_name`,
        `organizations`.`organization_name` AS `organization_name`,
        `report_types`.`report_type` AS `report_type`,
        `channels`.`channel_name` AS `channel_name`,
            COALESCE(
            `customers`.`customer_name`,
            `employees`.`employee_name`
            ) AS `customer_location`,
        `concepts`.`concept_name` AS `store_concept_name`,
        `gashapon_inventories`.`inventory_date` AS `inventory_date`,
        `gashapon_inventories`.`item_code` AS `item_code`,
        `gashapon_inventories`.`item_description` AS `item_description`,
        `items`.`digits_code` AS `digits_code`,
            COALESCE(
            `items`.`item_description`,
            `gashapon_inventories`.`item_description`
            ) AS `imfs_item_description`,
        `items`.`upc_code` AS `upc_code`,
        `items`.`upc_code2` AS `upc_code2`,
        `items`.`upc_code3` AS `upc_code3`,
        `items`.`upc_code4` AS `upc_code4`,
        `items`.`upc_code5` AS `upc_code5`,
        `items`.`brand_description` AS `brand_description`,
        `items`.`category_description` AS `category_description`,
        `items`.`margin_category_description` AS `margin_category_description`,
        `items`.`vendor_type_code` AS `vendor_type_code`,
        `items`.`inventory_type_description` AS `inventory_type_description`,
        `items`.`sku_status_description` AS `sku_status_description`,
        `items`.`brand_status` AS `brand_status`,
        `gashapon_inventories`.`quantity_inv` AS `quantity_inv`,
        `items`.`current_srp` AS `current_srp`, (
            `items`.`current_srp` *`gashapon_inventories`.`quantity_inv`
            ) AS `qtyinv_srp`,
        `gashapon_inventories`.`store_cost` AS `store_cost`,
        `gashapon_inventories`.`qtyinv_sc` AS `qtyinv_sc`,
        `gashapon_inventories`.`dtp_rf` AS `dtp_rf`,
        `gashapon_inventories`.`qtyinv_rf` AS `qtyinv_rf`,
        `gashapon_inventories`.`landed_cost` AS `landed_cost`,
        `gashapon_inventories`.`qtyinv_lc` AS `qtyinv_lc`
        FROM
        `gashapon_inventories`
            LEFT JOIN `systems` ON `gashapon_inventories`.`systems_id` =`systems`.`id`
            LEFT JOIN `organizations` ON `gashapon_inventories`.`organizations_id` =`organizations`.`id`
            LEFT JOIN `report_types` ON `gashapon_inventories`.`report_types_id` =`report_types`.`id`
            LEFT JOIN `channels` ON `gashapon_inventories`.`channels_id` =`channels`.`id`
            LEFT JOIN `customers` ON `gashapon_inventories`.`customers_id` =`customers`.`id`
            LEFT JOIN `concepts` ON `customers`.`concepts_id` =`concepts`.`id`
            LEFT JOIN `employees` ON `gashapon_inventories`.`employees_id` =`employees`.`id`
            LEFT JOIN `items` ON `gashapon_inventories`.`item_code` =`items`.`digits_code`;
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}