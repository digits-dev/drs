<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name',50)->nullable();
            $table->string('batch_file_location',100)->nullable();
            $table->text('batch_file_name')->nullable();
            $table->integer('total_records')->length(10)->unsigned()->nullable();
            $table->tinyInteger('is_download')->length(3)->unsigned()->default(0);
            $table->tinyInteger('is_final')->length(3)->unsigned()->default(0);
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
        Schema::dropIfExists('batch_uploads');
    }
}
