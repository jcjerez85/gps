<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TraccarDevicesMovedAtAddTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'moved_at')) { return; }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dateTime('moved_at')->nullable()->default(null);
        });

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::connection('traccar_mysql')->table('devices', function($table)
        {
            $table->dropColumn('moved_at');
        });
	}

}
