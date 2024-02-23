<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGachaSalesRunRatesTable extends Migration
{
    public $view_name = 'gacha_sales_run_rate';
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
                    store_sales.id,
                    0 as is_apple,
                    store_sales.channels_id,
                    store_sales.customers_id,
                    customers.concepts_id,
                    store_sales.employees_id,
                    store_sales.sales_date AS sales_date,
                    non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk,
                    non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff,
                    DATE_FORMAT(store_sales.sales_date, '%Y_%m') AS sales_date_yr_mo,
                    DATE_FORMAT(store_sales.sales_date, '%Y') AS sales_year,
                    DATE_FORMAT(store_sales.sales_date, '%m') AS sales_month,
                    store_sales.item_code AS item_code,
                    COALESCE(
                        store_sales.digits_code_rr_ref, store_sales.item_code
                    ) AS digits_code_rr_ref,
                    gacha_items.digits_code AS digits_code,
                    store_sales.quantity_sold AS quantity_sold
                FROM
                    store_sales
                    LEFT JOIN systems ON store_sales.systems_id = systems.id
                    LEFT JOIN organizations ON store_sales.organizations_id = organizations.id
                    LEFT JOIN report_types ON store_sales.report_types_id = report_types.id
                    LEFT JOIN channels ON store_sales.channels_id = channels.id
                    LEFT JOIN customers ON store_sales.customers_id = customers.id
                    LEFT JOIN concepts ON customers.concepts_id = concepts.id
                    LEFT JOIN employees ON store_sales.employees_id = employees.id
                    LEFT JOIN non_apple_cutoffs ON store_sales.sales_date = non_apple_cutoffs.sold_date
                    LEFT JOIN gacha_items ON store_sales.item_code = gacha_items.digits_code
                WHERE
                    store_sales.is_final = 1
                    AND gacha_items.digits_code IS NOT NULL
                    AND (store_sales.digits_code_rr_ref != 'GWP' OR store_sales.digits_code_rr_ref IS NULL)    
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS $this->view_name");
    }
}
