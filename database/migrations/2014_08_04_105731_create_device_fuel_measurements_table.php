<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceFuelMeasurementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('device_fuel_measurements')) { return; }

		Schema::create('device_fuel_measurements', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('fuel_title');
            $table->string('distance_title');
            $table->char('lang')->default('en')->index();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device_fuel_measurements');
	}

}
