<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceCheckValidBySpeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('devices', 'valid_by_avg_speed')) {
            Schema::table('devices', function ($table) {
                $table->boolean('valid_by_avg_speed')->after('gprs_templates_only')->default(true)->nullable();
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
        Schema::table('devices', function($table) {
            $table->dropColumn('valid_by_avg_speed');
        });
    }
}