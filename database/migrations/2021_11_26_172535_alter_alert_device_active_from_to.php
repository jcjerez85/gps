<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertDeviceActiveFromTo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('alert_device', 'active_from')) {
            Schema::table('alert_device', function ($table) {
                $table->dateTime('active_from')->nullable();
                $table->dateTime('active_to')->nullable();
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
        Schema::table('alert_device', function($table) {
            $table->dropColumn('active_from');
            $table->dropColumn('active_to');
        });
    }
}