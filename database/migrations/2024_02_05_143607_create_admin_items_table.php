<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->decimal('current_srp', 16, 2)->nullable();
            $table->string('status')->length(20)->default('ACTIVE')->nullable();
            $table->mediumInteger('created_by')->unsigned()->nullable();
            $table->mediumInteger('updated_by')->unsigned()->nullable();
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
        Schema::dropIfExists('admin_items');
    }
}