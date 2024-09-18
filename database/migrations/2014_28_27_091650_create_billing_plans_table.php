<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBillingPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('billing_plans')) { return; }

		Schema::create('billing_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->float('price')->unsigned();
			$table->integer('objects')->unsigned();
			$table->string('duration_type', 20);
			$table->integer('duration_value')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('billing_plans');
	}

}
