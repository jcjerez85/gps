<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterUsersDurationFormatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if( ! Schema::hasColumn('users', 'duration_format')) {
            Schema::table('users', function ($table) {
                $table->string('duration_format', 32)->nullable()->after('unit_of_capacity');
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
            $table->dropColumn('duration_format');
        });
	}

}
