<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UnregisteredDevicesLogCollection extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('traccar_mysql')->statement('ALTER TABLE `unregistered_devices_log` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
