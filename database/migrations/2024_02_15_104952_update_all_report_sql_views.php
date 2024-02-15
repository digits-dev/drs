<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAllReportSqlViews extends Migration
{
    public $view_names = [
        'store_sales_report',
        'digits_sales_report',
        'store_inventories_report',
        'warehouse_inventories_report',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->view_names as $view_name){
            $file_path = base_path("sql/$view_name.sql");

			if (file_exists($file_path)) {
				$file_contents = file_get_contents($file_path);
				DB::statement("DROP VIEW IF EXISTS $view_name;");
				DB::statement("
                    CREATE VIEW $view_name AS
                    $file_contents
                ");
			}
        }
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
