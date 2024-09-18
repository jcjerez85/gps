<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('device_groups')) { return; }

		Schema::create('device_groups', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('title');

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
		Schema::drop('device_groups');
	}

}
