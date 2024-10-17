<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('announcement_id'); 
            $table->unsignedInteger('users_id'); 
            $table->timestamps();

            // Indexes
            $table->index('announcement_id'); // Index for announcement_id
            $table->index('users_id'); // Index for users_id

            // Foreign key constraints can be added if needed, e.g.
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('users_id')->references('id')->on('cms_users')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_user');
    }
}
