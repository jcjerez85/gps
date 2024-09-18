<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceSensorSkipEmpty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('device_sensors', 'skip_empty')) {
            Schema::table('device_sensors', function ($table) {
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
        Schema::table('device_sensors', function($table) {
            $table->dropColumn('skip_empty');
        });
    }
}