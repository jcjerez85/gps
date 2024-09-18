<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterSilentEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasColumn('events', 'silent')) { return; }

        Schema::table('events', function ($table) {
            $table->boolean('silent')->nullable()->index();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('events', function ($table) {
            $table->dropColumn('silent');
        });
	}

}
