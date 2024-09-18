<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserDriverDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_driver_devices')) { return; }

		Schema::create('user_driver_devices', function(Blueprint $table)
		{
			$table->integer('device_id')->unsigned()->index();
			$table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
			$table->integer('driver_id')->unsigned()->index();
			$table->foreign('driver_id')->references('id')->on('user_drivers')->onDelete('cascade');
		});

        DB::insert('INSERT INTO user_driver_devices (device_id, driver_id) '
            . DB::table('user_drivers')
                ->select(['device_id', 'id AS driver_id'])
                ->whereNotNull('device_id')
                ->toSql()
        );
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_driver_devices');
	}

}
