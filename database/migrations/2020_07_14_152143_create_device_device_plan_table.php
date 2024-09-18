<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceDevicePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_device_plan')) {
            return;
        }

        Schema::create('device_device_plan', function(Blueprint $table) {
            $table->integer('device_id')
                ->unsigned();
            $table->integer('plan_id')
                ->unsigned();

            $table->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->onDelete('cascade');

            $table->foreign('plan_id')
                ->references('id')
                ->on('device_plans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('device_device_plan')) {
            return;
        }

        Schema::drop('device_device_plan');
    }
}
