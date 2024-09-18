<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportPoiPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('report_poi_pivot')) { return; }

		Schema::create('report_poi_pivot', function(Blueprint $table)
		{
			$table->integer('report_id')->unsigned()->index();
			$table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
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
		Schema::drop('report_poi_pivot');
	}

}
