<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('device_services')) { return; }

		Schema::create('device_services', function(Blueprint $table)
		{
            $table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('device_id')->unsigned()->nullable()->index();
            $table->string('name');
            $table->string('expiration_by')->nullable();
			$table->integer('interval')->default(1);
			$table->string('last_service')->nullable();
			$table->integer('trigger_event_left')->unsigned()->default(0);
            $table->boolean('renew_after_expiration')->default(0);
            $table->double('expires')->unsigned()->default(0);
            $table->date('expires_date')->nullable();
			$table->double('remind')->unsigned()->default(0);
			$table->date('remind_date')->nullable();
            $table->boolean('event_sent')->default(0)->index();
            $table->boolean('expired')->default(0)->index();
			$table->text('email')->nullable();
			$table->text('mobile_phone')->nullable();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
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
		Schema::drop('device_services');
	}

}
