<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertsTableAddStopDuation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('alerts', 'stop_duration'))
            return;

        Schema::table('alerts', function ($table) {
            $table->integer('stop_duration')->after('ac_alarm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alerts', function($table)
        {
            $table->dropColumn('stop_duration');
        });
    }
}
