<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveTraccarDevicesTable  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('traccar_devices'))
            return;

        DB::statement('CREATE TABLE `traccar_devices` LIKE `gpswox_traccar`.`devices`;');
        DB::statement('INSERT INTO `traccar_devices` SELECT * FROM `gpswox_traccar`.`devices`;');
        DB::statement('DROP TABLE `gpswox_traccar`.`devices`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('CREATE TABLE `gpswox_traccar`.`devices` LIKE `traccar_devices`;');
        DB::statement('INSERT INTO `gpswox_traccar`.`devices` SELECT * FROM `traccar_devices`;');
        DB::statement('DROP TABLE `traccar_devices`');
    }
}
