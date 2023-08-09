<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonAppleCutoffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_apple_cutoffs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('sold_date')->nullable();
            $table->string('day_cy',30)->nullable();
            $table->string('year_cy',30)->nullable();
            $table->string('month_cy',30)->nullable();
            $table->string('week_cy',30)->nullable();
            $table->string('non_apple_yr_mon_wk',100)->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('non_apple_week_cutoff',100)->nullable();
            $table->string('status', 10)->default('ACTIVE')->nullable();
            $table->integer('created_by')->length(10)->unsigned()->nullable();
            $table->integer('updated_by')->length(10)->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_apple_cutoffs');
    }
}
