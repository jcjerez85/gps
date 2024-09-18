<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertGeofenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('alert_geofence')) { return; }

		Schema::create('alert_geofence', function(Blueprint $table)
		{
			$table->increments('id');
            $table->tinyInteger('zone')->index();
			$table->integer('alert_id')->unsigned()->index();
			$table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
			$table->integer('geofence_id')->unsigned()->index();
			$table->foreign('geofence_id')->references('id')->on('geofences')->onDelete('cascade');
			$table->string('time_from', 5)->default('00:00');
			$table->string('time_to', 5)->default('00:00');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alert_geofence');
	}

}
