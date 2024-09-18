<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDevicePlanDeviceTypePivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('device_plan_device_type')) { return; }

		Schema::create('device_plan_device_type', function(Blueprint $table)
		{
            $table->integer('device_plan_id')->unsigned()->index();
            $table->foreign('device_plan_id')->references('id')->on('device_plans')->onDelete('cascade');
			$table->integer('device_type_id')->unsigned()->index();
			$table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device_plan_device_type');
	}

}
