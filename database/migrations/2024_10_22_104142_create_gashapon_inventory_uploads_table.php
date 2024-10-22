<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGashaponInventoryUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gashapon_inventory_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('status')->length(50)->nullable()->default('FILE UPLOADED');
            $table->string('batch')->length(20)->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('job_batches_id')->length(255)->nullable();
            $table->string('folder_name')->nullable();
            $table->string('file_name')->nullable();
            $table->text('file_path')->nullable();
            $table->text('generated_file_path')->nullable();
            $table->mediumInteger('row_count')->nullable()->unsigned();
            $table->mediumInteger('chunk_count')->nullable()->unsigned();
            $table->json('headings')->nullable();
            $table->json('errors')->nullable();
            $table->tinyInteger('is_final')->unsigned()->nullable()->default(0);
            $table->mediumInteger('tagged_as_final_by')->unsigned()->nullable();
            $table->timestamp('tagged_as_final_at')->nullable();
            $table->mediumInteger('created_by')->nullable()->unsigned();
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
        Schema::dropIfExists('gashapon_inventory_uploads');
    }
}
