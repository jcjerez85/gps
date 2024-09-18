<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertPoiTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('alert_poi')) { return; }

		Schema::create('alert_poi', function(Blueprint $table)
		{
			$table->integer('alert_id')->unsigned()->index();
			$table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
			$table->integer('poi_id')->unsigned()->index();
			$table->foreign('poi_id')->references('id')->on('user_map_icons')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alert_poi');
	}

}
