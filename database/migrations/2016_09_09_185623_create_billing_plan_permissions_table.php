<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBillingPlanPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('billing_plan_permissions')) { return; }

		Schema::create('billing_plan_permissions', function(Blueprint $table)
		{
			$table->integer('plan_id')->unsigned()->index();
			$table->string('name', 50);
			$table->boolean('view')->default('0');
			$table->boolean('edit')->default('0');
			$table->boolean('remove')->default('0');

			$table->foreign('plan_id')->references('id')->on('billing_plans')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('billing_plan_permissions');
	}

}
