<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveUnregisteredDevicesLogTable  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('unregistered_devices_log'))
            return;

        DB::statement('CREATE TABLE `unregistered_devices_log` LIKE `gpswox_traccar`.`unregistered_devices_log`;');
        DB::statement('INSERT INTO `unregistered_devices_log` SELECT * FROM `gpswox_traccar`.`unregistered_devices_log`;');
        DB::statement('DROP TABLE `gpswox_traccar`.`unregistered_devices_log`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('CREATE TABLE `gpswox_traccar`.`unregistered_devices_log` LIKE `unregistered_devices_log`;');
        DB::statement('INSERT INTO `gpswox_traccar`.`unregistered_devices_log` SELECT * FROM `unregistered_devices_log`;');
        DB::statement('DROP TABLE `unregistered_devices_log`');
    }
}
