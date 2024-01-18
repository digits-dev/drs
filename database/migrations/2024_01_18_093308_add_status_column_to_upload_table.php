<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->string('status')->length(50)->nullable()->default('FILE UPLOADED')->after('id');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->string('status')->length(50)->nullable()->default('FILE UPLOADED')->after('id');
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
            $table->dropColumn('status');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
