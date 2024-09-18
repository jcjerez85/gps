<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandScheduleDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('command_schedule_device')) { return; }

        Schema::create('command_schedule_device', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('command_schedule_id')->unsigned();
            $table->integer('device_id')->unsigned();
            $table->timestamps();

            $table->foreign('command_schedule_id')->references('id')->on('command_schedules')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('command_schedule_device');
    }
}
