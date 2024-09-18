<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGeofenceGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('geofence_groups')) { return; }

		Schema::create('geofence_groups', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('title');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
		Schema::drop('geofence_groups');
	}

}
