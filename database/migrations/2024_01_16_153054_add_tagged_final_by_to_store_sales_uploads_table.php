<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaggedFinalByToStoreSalesUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->mediumInteger('tagged_as_final_by')->nullable()->unsigned()->after('is_final');
            $table->timestamp('tagged_as_final_at')->nullable()->after('tagged_as_final_by');
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
            $table->dropColumn('tagged_as_final_by');
            $table->dropColumn('tagged_as_final_at');
        });
    }
}
