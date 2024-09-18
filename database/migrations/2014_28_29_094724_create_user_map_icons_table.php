<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserMapIconsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('user_map_icons')) { return; }

		Schema::create('user_map_icons', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('map_icon_id')->unsigned();
            $table->boolean('active')->index()->default(1);
            $table->string('name');
            $table->text('description');
            $table->text('coordinates');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('map_icon_id')->references('id')->on('map_icons')->onDelete('cascade');
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
		Schema::drop('user_map_icons');
	}

}
