<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'device_type_id'))
            return;

        Schema::table('devices', function(Blueprint $table) {
            $table->integer('device_type_id')->unsigned()->nullable()->default(null);
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('devices', 'device_type_id')) {
            return;
        }

        Schema::table('devices', function($table) {
            $table->dropColumn('device_type_id');
        });
    }
}
