<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceSensorDecbin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('device_sensors', 'decbin')) {
            Schema::table('device_sensors', function ($table) {
                $table->boolean('decbin')->nullable();
            });
        }

        if( ! Schema::hasColumn('sensor_group_sensors', 'decbin')) {
            Schema::table('sensor_group_sensors', function ($table) {
                $table->boolean('decbin')->nullable();
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
            $table->dropColumn('decbin');
        });

        Schema::table('sensor_group_sensors', function($table) {
            $table->dropColumn('decbin');
        });
    }
}