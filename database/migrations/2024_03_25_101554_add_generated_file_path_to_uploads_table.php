<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneratedFilePathToUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_sales_uploads', function (Blueprint $table) {
            $table->text('generated_file_path')->nullable()->after('file_path');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->text('generated_file_path')->nullable()->after('file_path');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->text('generated_file_path')->nullable()->after('file_path');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->text('generated_file_path')->nullable()->after('file_path');
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
            $table->dropColumn('generated_file_path');
        });
        Schema::table('digits_sales_uploads', function (Blueprint $table) {
            $table->dropColumn('generated_file_path');
        });
        Schema::table('store_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('generated_file_path');
        });
        Schema::table('warehouse_inventory_uploads', function (Blueprint $table) {
            $table->dropColumn('generated_file_path');
        });
    }
}
