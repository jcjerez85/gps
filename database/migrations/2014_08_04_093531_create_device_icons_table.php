<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceIconsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('device_icons')) { return; }
        
		Schema::create('device_icons', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('type', 20)->default('icon');
            $table->tinyInteger('order')->nullable();
            $table->float('width');
            $table->float('height');
			$table->string('path');
            $table->boolean('by_status')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device_icons');
	}

}
