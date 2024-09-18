<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTimezonesTable extends Migration {
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('timezones')) { return; }

		Schema::create('timezones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->float('order')->index()->unsigned();
            $table->string('title');
            $table->string('zone', 20)->nullable();
			$table->string('prefix', 5)->nullable();
			$table->string('time', 5)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('timezones');
	}

}
