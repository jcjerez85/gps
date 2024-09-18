<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'msisdn')) {
            return;
        }

        Schema::table('devices', function ($table) {
            $table->string('msisdn')
                ->nullable()
                ->after('sim_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('devices', 'msisdn')) {
            return;
        }

        Schema::table('devices', function($table) {
            $table->dropColumn('msisdn');
        });
    }
}
