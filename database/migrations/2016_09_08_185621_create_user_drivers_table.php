<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_drivers')) { return; }

		Schema::create('user_drivers', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('device_id')->unsigned()->nullable()->index();
            $table->string('device_port', '10')->nullable();
            $table->string('name')->nullable();
            $table->string('rfid')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('description')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
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
		Schema::drop('user_drivers');
	}

}
