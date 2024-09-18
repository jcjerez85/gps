<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDevicePivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_device_pivot')) { return; }

		Schema::create('user_device_pivot', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('device_id')->unsigned()->index();
			$table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->integer('group_id')->unsigned()->nullable()->index();
            $table->foreign('group_id')->references('id')->on('device_groups')->onDelete('set null');
            $table->integer('current_driver_id')->unsigned()->nullable()->index();
            $table->foreign('current_driver_id')->references('id')->on('user_drivers')->onDelete('set null');
            $table->boolean('active')->index()->default(1);
            $table->text('current_geofences')->nullable();
            $table->text('current_events')->nullable();
			$table->integer('timezone_id')->unsigned()->nullable()->index();
			$table->foreign('timezone_id')->references('id')->on('timezones')->onDelete('set null');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_device_pivot');
	}

}
