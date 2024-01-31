<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunRateView extends Migration
{
    public $view_name = 'run_rate';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS $this->view_name");
        DB::statement("CREATE VIEW $this->view_name AS
            SELECT
                digits_code_rr_ref,
                sum(quantity_sold) as quantity_sold,
                channel_name,
                customer_location,
                store_concept_name,
                sales_date,
                apple_yr_qtr_wk,
                apple_week_cutoff,
                non_apple_yr_mon_wk,
                non_apple_week_cutoff,
                sales_date_yr_mo,
                sales_year,
                sales_month
            from store_sales_report
            where exists (
                select digits_code from items where items.digits_code = digits_code_rr_ref
            )
            and is_final = 1
            group by digits_code_rr_ref,
                channel_name,
                customer_location,
                store_concept_name,
                sales_date,
                apple_yr_qtr_wk,
                apple_week_cutoff,
                non_apple_yr_mon_wk,
                non_apple_week_cutoff,
                sales_date_yr_mo,
                sales_year,
                sales_month        
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
