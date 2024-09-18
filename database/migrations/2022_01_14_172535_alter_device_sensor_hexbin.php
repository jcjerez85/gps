<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceSensorHexbin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('device_sensors', 'hexbin')) {
            Schema::table('device_sensors', function ($table) {
                $table->boolean('hexbin')->nullable();
            });
        }

        if( ! Schema::hasColumn('sensor_group_sensors', 'hexbin')) {
            Schema::table('sensor_group_sensors', function ($table) {
                $table->boolean('hexbin')->nullable();
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
        Schema::table('device_sensors', function($table) {
            $table->dropColumn('hexbin');
        });

        Schema::table('sensor_group_sensors', function($table) {
            $table->dropColumn('hexbin');
        });
    }
}