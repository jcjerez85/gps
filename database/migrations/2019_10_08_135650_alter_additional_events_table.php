<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAdditionalEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasColumn('events', 'additional')) { return; }

        Schema::table('events', function ($table) {
            $table->text('additional')->nullable()->default(null);
        });

        DB::table('events')
            ->whereIn('type', ['overspeed', 'stop_duration', 'offline_duration', 'idle_duration'])
            ->update([
                'additional' => DB::raw('message'),
                'message'    => DB::raw('NULL'),
            ]);

        DB::table('events')
            ->whereIn('type', ['driver'])
            ->update([
                'additional' => DB::raw('REPLACE(\'{"driver_name":"[driver]"}\', \'[driver]\', message)'),
                'message'    => DB::raw('NULL'),
            ]);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
