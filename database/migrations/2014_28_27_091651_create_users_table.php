<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('users')) { return; }

		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->boolean('active')->default('1')->index();
			$table->integer('group_id')->unsigned()->nullable()->index();
            $table->integer('manager_id')->unsigned()->nullable()->index();
            $table->integer('billing_plan_id')->unsigned()->nullable()->index();
            $table->integer('map_id')->unsigned()->nullable();
            $table->integer('devices_limit')->unsigned()->nullable()->index();
			$table->string('email')->unique();
            $table->string('password', 64);
			$table->string('remember_token', 64)->nullable();
            $table->timestamp('subscription_expiration')->default('0000-00-00 00:00:00');
            $table->timestamp('loged_at')->default('0000-00-00 00:00:00')->index();
			$table->string('api_hash')->nullable()->unique();
            $table->string('available_maps')->nullable();
			$table->timestamp('sms_gateway_app_date')->default('0000-00-00 00:00:00');
			$table->text('sms_gateway_params')->nullable();
			$table->text('open_geofence_groups')->nullable();
			$table->text('open_device_groups')->nullable();
			$table->tinyInteger('week_start_day')->default('1');
			$table->tinyInteger('top_toolbar_open')->default('1');
            $table->string('map_controls', 500)->default('{}');
			$table->timestamps();

            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('billing_plan_id')->references('id')->on('billing_plans')->onDelete('set null');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
