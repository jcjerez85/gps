<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if( ! Schema::hasColumn('users', 'lang')) {
            Schema::table('users', function ($table) {
                $table->char('lang', 2)->default('en');
                $table->char('unit_of_distance', 2)->default('km');
                $table->char('unit_of_capacity', 2)->default('lt');
                $table->integer('timezone_id')->default('57')->unsigned()->index();
            });
        }
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('users', function($table)
        {
            $table->dropColumn('lang');
            $table->dropColumn('unit_of_distance');
            $table->dropColumn('unit_of_capacity');
            $table->dropColumn('timezone_id');
        });
	}

}
