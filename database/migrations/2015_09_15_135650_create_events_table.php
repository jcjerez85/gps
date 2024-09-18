<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('events')) { return; }

		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('device_id')->unsigned()->nullable();
            $table->integer('geofence_id')->unsigned()->nullable();
            $table->integer('position_id')->unsigned()->nullable();
            $table->integer('alert_id')->unsigned()->nullable();
            $table->string('type', 30)->nullable();
            $table->string('message');
            $table->string('address', 500)->collate('utf8mb4_general_ci')->nullable();
            $table->double('altitude')->nullable();
            $table->double('course')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('power')->nullable();
            $table->double('speed')->nullable();
            $table->datetime('time')->nullable();
            $table->boolean('deleted')->index()->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('geofence_id')->references('id')->on('geofences')->onDelete('set null');
            $table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
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
		Schema::drop('events');
	}

}
