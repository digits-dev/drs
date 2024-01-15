<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSalesUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sales_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('batch')->length(20)->nullable();
            $table->string('job_batches_id')->length(255)->nullable();
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->text('file_path')->nullable();
            $table->mediumInteger('row_count')->nullable()->unsigned();
            $table->mediumInteger('chunk_count')->nullable()->unsigned();
            $table->json('headings')->nullable();
            $table->mediumInteger('created_by')->nullable()->unsigned();
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
        Schema::dropIfExists('store_sales_uploads');
    }
}
