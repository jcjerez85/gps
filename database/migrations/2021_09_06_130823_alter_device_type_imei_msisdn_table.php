<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceTypeImeiMsisdnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('device_type_imeis', 'msisdn')) {
            return;
        }

        Schema::table('device_type_imeis', function ($table) {
            $table->string('msisdn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('device_type_imeis', 'msisdn')) {
            return;
        }

        Schema::table('device_type_imeis', function ($table) {
            $table->dropColumn('msisdn');
        });
    }
}
