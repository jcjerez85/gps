<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterUserDriverPositionPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `user_driver_position_pivot` CHANGE `device_id` `device_id` INT(10) UNSIGNED NULL;');
        DB::statement('ALTER TABLE `user_driver_position_pivot` CHANGE `driver_id` `driver_id` INT(10) UNSIGNED NULL;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `user_driver_position_pivot` CHANGE `device_id` `device_id` INT(10) UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE `user_driver_position_pivot` CHANGE `driver_id` `driver_id` INT(10) UNSIGNED NOT NULL;');
    }

}
