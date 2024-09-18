<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUserGprsTemplateDeviceTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_gprs_template_device_types')) { return; }

		Schema::create('user_gprs_template_device_types', function(Blueprint $table)
		{
			$table->integer('device_type_id')->unsigned()->index();
			$table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
			$table->integer('user_gprs_template_id')->unsigned()->index();
			$table->foreign('user_gprs_template_id')->references('id')->on('user_gprs_templates')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_gprs_template_device_types');
	}

}
