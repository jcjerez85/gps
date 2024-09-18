<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('reports')) { return; }

		Schema::create('reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('title')->nullable();
			$table->tinyInteger('type')->nullable()->unsigned();
			$table->string('format', 10)->nullable();
			$table->boolean('show_addresses')->nullable();
			$table->boolean('zones_instead')->nullable();
			$table->tinyInteger('stops')->nullable()->unsigned();
			$table->float('speed_limit')->nullable()->unsigned();
			$table->boolean('daily')->nullable()->index();
			$table->string('daily_time', 5)->default('00:00');
			$table->boolean('weekly')->nullable()->index();
			$table->string('weekly_time', 5)->default('00:00');
			$table->text('email')->nullable();
			$table->datetime('weekly_email_sent')->nullable()->index();
			$table->datetime('daily_email_sent')->nullable()->index();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
		Schema::drop('reports');
	}

}
