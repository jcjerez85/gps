<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionGeofenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('position_geofence')) { return; }

		Schema::create('position_geofence', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('position_id')->unsigned()->index();
			$table->integer('geofence_id')->unsigned()->index();
			$table->foreign('geofence_id')->references('id')->on('geofences')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('position_geofence');
	}

}
