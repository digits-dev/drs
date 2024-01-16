<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFinalToStoreSalesUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->tinyInteger('is_final')->nullable()->unsigned()->default(0)->after('headings');
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
            $table->dropColumn('is_final');
        });
    }
}
