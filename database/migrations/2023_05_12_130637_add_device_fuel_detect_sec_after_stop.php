<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceFuelDetectSecAfterStop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('devices', 'fuel_detect_sec_after_stop')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->unsignedSmallInteger('fuel_detect_sec_after_stop')->nullable();
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
        if (Schema::hasColumn('devices', 'fuel_detect_sec_after_stop')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->dropColumn('fuel_detect_sec_after_stop');
            });
        }
    }
}
