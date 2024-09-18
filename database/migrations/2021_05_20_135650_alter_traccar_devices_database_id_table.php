<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTraccarDevicesDatabaseIdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'database_id')) { return; }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->integer('database_id')->unsigned()->nullable()->default(null);
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
            $table->dropColumn('database_id');
        });
	}

}
