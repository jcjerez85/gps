<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventCustomTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('event_custom_tags')) { return; }

		Schema::create('event_custom_tags', function(Blueprint $table)
		{
            $table->integer('event_custom_id')->unsigned()->index();
            $table->foreign('event_custom_id')->references('id')->on('events_custom')->onDelete('cascade');
			$table->string('tag')->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_custom_tags');
	}

}
