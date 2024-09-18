<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertEventPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('alert_event_pivot')) { return; }

		Schema::create('alert_event_pivot', function(Blueprint $table)
		{
			$table->integer('alert_id')->unsigned()->index();
			$table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
			$table->integer('event_id')->unsigned()->index();
			$table->foreign('event_id')->references('id')->on('events_custom')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alert_event_pivot');
	}

}
