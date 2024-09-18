<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDriverPositionPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_driver_position_pivot')) { return; }

		Schema::create('user_driver_position_pivot', function(Blueprint $table)
		{
            $table->integer('device_id')->unsigned()->index();
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
			$table->integer('driver_id')->unsigned()->index();
			$table->foreign('driver_id')->references('id')->on('user_drivers')->onDelete('cascade');
            $table->datetime('date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_driver_position_pivot');
	}

}
