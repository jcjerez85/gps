<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateGeofencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('geofences')) { return; }

		Schema::create('geofences', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();
			$table->integer('group_id')->unsigned()->index()->nullable();
            $table->boolean('active')->index()->default(1);
            $table->string('name');
            $table->text('coordinates');
            $table->string('polygon_color', 7);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->timestamps();

			$table->foreign('group_id')->references('id')->on('geofence_groups')->onDelete('set null');
		});

        DB::statement("ALTER TABLE geofences ADD COLUMN polygon POLYGON after coordinates");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('geofences');
	}

}
