<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSubmasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('vendor_name')->nullable()->after('vendor_type_code');
            });

            Schema::table('admin_items', function (Blueprint $table) {
                $table->string('vendor_name')->nullable()->after('vendor_type_code');
            });
    
            Schema::table('rma_items', function (Blueprint $table) {
                $table->string('vendor_name')->nullable()->after('vendor_type_code');
            });
    
            Schema::table('gacha_items', function (Blueprint $table) {
                $table->string('vendor_name')->nullable()->after('vendor_type_code');
            });
    
            Schema::table('service_items', function (Blueprint $table) {
                $table->string('vendor_name')->nullable()->after('vendor_type_code');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('vendor_name');
            });
            Schema::table('admin_items', function (Blueprint $table) {
                $table->dropColumn('vendor_name');
            });
            Schema::table('rma_items', function (Blueprint $table) {
                $table->dropColumn('vendor_name');
            });
            Schema::table('gacha_items', function (Blueprint $table) {
                $table->dropColumn('vendor_name');
            });
            Schema::table('service_items', function (Blueprint $table) {
                $table->dropColumn('vendor_name');
            });
        });
    }
}
