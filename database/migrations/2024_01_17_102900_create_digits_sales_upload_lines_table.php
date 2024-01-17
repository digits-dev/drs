<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigitsSalesUploadLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digits_sales_upload_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('digits_sales_uploads_id')->unsigned()->nullable();
            $table->mediumInteger('chunk_index')->unsigned()->nullable();
            $table->json('chunk_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digits_sales_upload_lines');
    }
}
