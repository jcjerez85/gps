<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

class CreateTimezonesDstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('timezones_dst')) { return; }

		Schema::create('timezones_dst', function(Blueprint $table)
		{
            $table->increments('id');
			$table->string('country', 50)->nullable();
			$table->string('from_period', 50)->nullable();
			$table->string('from_time', 5)->nullable();
			$table->string('to_period', 50)->nullable();
			$table->string('to_time', 5)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('timezones_dst');
	}

}
