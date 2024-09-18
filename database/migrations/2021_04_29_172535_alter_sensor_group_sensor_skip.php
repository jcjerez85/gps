<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSensorGroupSensorSkip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('sensor_group_sensors', 'skip_calibration')) {
            Schema::table('sensor_group_sensors', function ($table) {
                $table->boolean('skip_calibration')->nullable();
                $table->boolean('skip_empty')->nullable();
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
        Schema::table('sensor_group_sensors', function($table) {
            $table->dropColumn('skip_calibration');
            $table->dropColumn('skip_empty');
        });
    }
}