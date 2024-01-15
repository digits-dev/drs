<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoreSalesReportView extends Migration
{
    private $view_name = 'store_sales_report';
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS $this->view_name");
        DB::statement("
            CREATE VIEW $this->view_name AS
            SELECT
                `store_sales`.`id`,
                `store_sales`.`batch_number`,
                `store_sales`.`reference_number`, (
                    CASE
                        WHEN(`items`.`digits_code` IS NULL) THEN 0
                        ELSE 1
                    end
                ) AS `rr_flag`,
                `systems`.`system_name` AS `system_name`,
                `organizations`.`organization_name` AS `organization_name`,
                `report_types`.`report_type` AS `report_type`,
                `channels`.`channel_name` AS `channel_name`,
                COALESCE(
                    `customers`.`customer_name`,
                    `employees`.`employee_name`
                ) AS `customer_location`,
                `concepts`.`concept_name` AS `store_concept_name`,
                `store_sales`.`receipt_number` AS `receipt_number`,
                `store_sales`.`sales_date` AS `sales_date`,
                `apple_cutoffs`.`apple_yr_qtr_wk` AS `apple_yr_qtr_wk`,
                `apple_cutoffs`.`apple_week_cutoff` AS `apple_week_cutoff`,
                `non_apple_cutoffs`.`non_apple_yr_mon_wk` AS `non_apple_yr_mon_wk`,
                `non_apple_cutoffs`.`non_apple_week_cutoff` AS `non_apple_week_cutoff`,
                CONCAT(
                    EXTRACT(
                        YEAR
                        FROM
                            `store_sales`.`sales_date`
                    ),
                    '_',
                    LPAD(
                        EXTRACT(
                            MONTH
                            FROM
                                `store_sales`.`sales_date`
                        ),
                        2,
                        0
                    )
                ) AS `sales_date_yr_mo`,
                EXTRACT(
                    YEAR
                    FROM
                        `store_sales`.`sales_date`
                ) AS `sales_year`,
                LPAD(
                    EXTRACT(
                        MONTH
                        FROM
                            `store_sales`.`sales_date`
                    ),
                    2,
                    0
                ) AS `sales_month`,
                `store_sales`.`item_code` AS `item_code`,
                `store_sales`.`item_description` AS `item_description`,
                COALESCE(
                    `store_sales`.`digits_code_rr_ref`,
                    `store_sales`.`item_code`
                ) AS `digits_code_rr_ref`,
                `items`.`digits_code` AS `digits_code`,
                COALESCE(
                    `items`.`item_description`,
                    `store_sales`.`item_description`
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
                `store_sales`.`quantity_sold` AS `quantity_sold`,
                `store_sales`.`sold_price` AS `sold_price`,
                `store_sales`.`qtysold_price` AS `qtysold_price`,
                `store_sales`.`store_cost` AS `store_cost`,
                `store_sales`.`qtysold_sc` AS `qtysold_sc`,
                `store_sales`.`net_sales` AS `net_sales`,
                `store_sales`.`sale_memo_reference` AS `sale_memo_reference`,
                `store_sales`.`current_srp` AS `current_srp`,
                `store_sales`.`qtysold_csrp` AS `qtysold_csrp`,
                `store_sales`.`dtp_rf` AS `dtp_rf`,
                `store_sales`.`qtysold_rf` AS `qtysold_rf`,
                `store_sales`.`landed_cost` AS `landed_cost`,
                `store_sales`.`qtysold_lc` AS `qtysold_lc`,
                `store_sales`.`dtp_ecom` AS `dtp_ecom`,
                `store_sales`.`qtysold_ecom` AS `qtysold_ecom`
            FROM `store_sales`
                LEFT JOIN `systems` ON `store_sales`.`systems_id` = `systems`.`id`
                LEFT JOIN `organizations` ON `store_sales`.`organizations_id` = `organizations`.`id`
                LEFT JOIN `report_types` ON `store_sales`.`report_types_id` = `report_types`.`id`
                LEFT JOIN `channels` ON `store_sales`.`channels_id` = `channels`.`id`
                LEFT JOIN `customers` ON `store_sales`.`customers_id` = `customers`.`id`
                LEFT JOIN `concepts` ON `customers`.`concepts_id` = `concepts`.`id`
                LEFT JOIN `employees` ON `store_sales`.`employees_id` = `employees`.`id`
                LEFT JOIN `apple_cutoffs` ON `store_sales`.`sales_date` = `apple_cutoffs`.`sold_date`
                LEFT JOIN `non_apple_cutoffs` ON `store_sales`.`sales_date` = `non_apple_cutoffs`.`sold_date`
                LEFT JOIN `items` ON `store_sales`.`item_code` = `items`.`digits_code`;      
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS $this->view_name");
    }
}
