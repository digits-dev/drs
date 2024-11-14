<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::create('target_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stores_id')->nullable();
            $table->year('year')->nullable();
            $table->string('month')->nullable();
            $table->decimal('target_sales', 16, 2)->nullable();
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
        Schema::dropIfExists('target_sales');
    }
}