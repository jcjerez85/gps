<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSensorGroupIdDeviceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('device_types', 'sensor_group_id'))
            return;

        Schema::table('device_types', function(Blueprint $table) {
            $table->integer('sensor_group_id')->unsigned()->nullable()->default(null);
            $table->foreign('sensor_group_id')->references('id')->on('sensor_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('device_types', 'sensor_group_id')) {
            return;
        }

        Schema::table('device_types', function($table) {
            $table->dropForeign(['sensor_group_id']);
            $table->dropColumn('sensor_group_id');
        });
    }
}
