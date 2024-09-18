<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersDstTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('users_dst')) { return; }

		Schema::create('users_dst', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned()->index();
			$table->integer('country_id')->nullable()->unsigned()->index();
			$table->string('type', 10)->nullable()->index();
			$table->string('date_from', 11)->nullable();
			$table->string('date_to', 11)->nullable();
			$table->string('month_from', 15)->nullable();
			$table->string('week_pos_from', 15)->nullable();
			$table->string('week_day_from', 15)->nullable();
			$table->string('time_from', 5)->nullable();
			$table->string('month_to', 15)->nullable();
			$table->string('week_pos_to', 15)->nullable();
			$table->string('week_day_to', 15)->nullable();
			$table->string('time_to', 5)->nullable();

			$table->primary(array('user_id', 'type'));
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_dst');
	}

}
