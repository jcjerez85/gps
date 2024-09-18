<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpeedLimitToGeofencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('geofences', 'speed_limit')) {
            return;
        }

        Schema::table('geofences', function (Blueprint $table) {
            $table->float('speed_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('geofences', 'speed_limit')) {
            return;
        }

        Schema::table('geofences', function (Blueprint $table) {
            $table->dropColumn('speed_limit');
        });
    }
}
