<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_permissions')) { return; }

		Schema::create('user_permissions', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned()->index();
			$table->string('name', 50);
			$table->boolean('view')->default('0');
			$table->boolean('edit')->default('0');
			$table->boolean('remove')->default('0');

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
		Schema::drop('user_permissions');
	}

}
