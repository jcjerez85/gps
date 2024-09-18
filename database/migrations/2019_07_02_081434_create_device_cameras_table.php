<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('device_cameras')) { 
            Schema::create('device_cameras', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('device_id')->unsigned()->index();
                $table->string('name');
                $table->boolean('show_widget')->default(0);
                $table->string('ftp_username');
                $table->string('ftp_password');
                $table->timestamps();

                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_cameras');
    }
}
