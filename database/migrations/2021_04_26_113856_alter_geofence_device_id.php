<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGeofenceDeviceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('geofences', 'device_id'))
            return;

        Schema::table('geofences', function(Blueprint $table) {
            $table->integer('device_id')->unsigned()->nullable()->default(null);
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('geofences', 'device_id')) {
            return;
        }

        Schema::table('geofences', function($table) {
            $table->dropColumn('device_id');
        });
    }
}
