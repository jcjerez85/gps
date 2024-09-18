<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('devices')) { return; }

		Schema::create('devices', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->biginteger('traccar_device_id')->index();
            $table->integer('icon_id')->unsigned()->nullable();
            $table->string('icon_colors')->default('{"moving":"green","stopped":"yellow","offline":"red","engine":"yellow"}');
            $table->boolean('active')->index()->default(1);
            $table->boolean('deleted')->index()->default(0);
			$table->string('name');
			$table->string('imei')->unique();
            $table->integer('fuel_measurement_id')->unsigned()->nullable();
            $table->decimal('fuel_quantity', 8, 2);
            $table->decimal('fuel_price', 8, 2);
            $table->decimal('fuel_per_km', 8, 2);
            $table->string('sim_number')->nullable();
            $table->string('device_model')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('object_owner')->nullable();
            $table->date('expiration_date')->default('0000-00-00');
            $table->char('tail_color', 7)->default('#33CC33')->nullable();
            $table->integer('tail_length')->default(5)->nullable();
            $table->string('engine_hours', 30)->default('gps')->nullable();
            $table->string('detect_engine', 30)->default('gps')->nullable();
            $table->integer('min_moving_speed')->default(6)->unsigned()->nullable();
            $table->integer('min_fuel_fillings')->default(10)->unsigned()->nullable();
            $table->integer('min_fuel_thefts')->default(10)->unsigned()->nullable();
            $table->boolean('snap_to_road')->default(0);
            $table->boolean('gprs_templates_only')->default(0);
            $table->text('parameters');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('icon_id')->references('id')->on('device_icons')->onDelete('set null');
            $table->foreign('fuel_measurement_id')->references('id')->on('device_fuel_measurements')->onDelete('set null');
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
		Schema::drop('devices');
	}

}
