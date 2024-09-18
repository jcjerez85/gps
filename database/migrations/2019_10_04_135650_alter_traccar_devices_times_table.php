<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTraccarDevicesTimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'stoped_at')) { return; }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dateTime('stoped_at')->nullable()->default(null);
            $table->dateTime('engine_on_at')->nullable()->default(null);
            $table->dateTime('engine_off_at')->nullable()->default(null);
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
            $table->dropColumn('stoped_at');
            $table->dropColumn('engine_on_at');
            $table->dropColumn('engine_off_at');
        });
	}

}
