<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertFuelConsumptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('alert_fuel_consumption')) { return; }

		Schema::create('alert_fuel_consumption', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('alert_id')->unsigned()->nullable();
            $table->integer('quantity');
            $table->tinyInteger('fuel_type');
            $table->date('from');
            $table->date('to');
			$table->boolean('done');
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
		Schema::drop('alert_fuel_consumption');
	}

}
