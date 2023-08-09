<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportHeadersToReportPrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_privileges', function (Blueprint $table) {
            $table->longText('report_header')->nullable()->after('report_query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_privileges', function (Blueprint $table) {
            $table->dropColumn('report_header');
        });
    }
}
