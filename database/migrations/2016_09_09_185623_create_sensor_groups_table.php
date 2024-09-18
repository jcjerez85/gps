<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSensorGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('sensor_groups')) { return; }

		Schema::create('sensor_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 100)->index();
			$table->integer('count');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sensor_groups');
	}

}
