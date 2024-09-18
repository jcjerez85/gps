<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsStops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `reports` CHANGE `stops` `stops` INT(10) UNSIGNED;');

        DB::table('reports')->whereNotNull('stops')->update([
            'stops' =>  DB::raw('stops * 60')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `reports` CHANGE `stops` `stops` TINYINT(3) UNSIGNED;');

        DB::table('reports')->whereNotNull('stops')->update([
            'stops' =>  DB::raw('stops * 60')
        ]);
    }
}