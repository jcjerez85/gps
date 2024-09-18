<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceSensorSkipCalibration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('device_sensors', 'skip_calibration')) {
            Schema::table('device_sensors', function ($table) {
                $table->boolean('skip_calibration')->nullable();
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
            $table->dropColumn('skip_calibration');
        });
    }
}