<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertDeviceStartedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('alert_device', 'started_at')) {
            Schema::table('alert_device', function ($table) {
                $table->dateTime('started_at')->nullable();
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
            $table->dropColumn('started_at');
        });
    }
}