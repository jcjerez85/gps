<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertDeviceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('alert_device')) { return; }

		Schema::create('alert_device', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('alert_id')->unsigned()->index();
			$table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
			$table->integer('device_id')->unsigned()->index();
			$table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('overspeed');
			//$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alert_device');
	}

}
