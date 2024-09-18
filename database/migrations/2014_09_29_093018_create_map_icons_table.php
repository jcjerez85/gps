<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMapIconsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('map_icons')) { return; }

		Schema::create('map_icons', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('path');
            $table->float('width');
            $table->float('height');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('map_icons');
	}

}
