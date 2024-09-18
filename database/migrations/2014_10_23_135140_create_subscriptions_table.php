<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('subscriptions')) { return; }

		Schema::create('subscriptions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id')->unsigned()->nullable(false);
            $table->integer('billing_plan_id')->unsigned()->nullable(false);
            $table->string('gateway');
            $table->string('gateway_id');
            $table->date('expiration_date')->default('0000-00-00');
            $table->boolean('active')->default(false);
            $table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('subscriptions');
	}

}
