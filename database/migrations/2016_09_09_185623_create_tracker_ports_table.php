<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrackerPortsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('tracker_ports')) { return; }

		Schema::create('tracker_ports', function(Blueprint $table)
		{
			$table->boolean('active')->default('1')->index();
			$table->string('port', 20)->unique();
			$table->string('name', 50)->unique();
			$table->text('extra');

			//$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tracker_ports');
	}

}
