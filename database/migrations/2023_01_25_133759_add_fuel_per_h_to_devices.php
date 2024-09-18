<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\DeviceFuelMeasurement;

class AddFuelPerHToDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'fuel_per_h')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->decimal('fuel_per_h', 10, 4)->after('fuel_per_km');
        });

        DeviceFuelMeasurement::updateOrCreate(['title' => 'kW/km'], [
            'title' => 'kW/km',
            'fuel_title' => 'kilometer',
            'distance_title' => 'Kilowatt-hours'
        ]);

        DeviceFuelMeasurement::updateOrCreate(['title' => 'l/h'], [
            'title' => 'l/h',
            'fuel_title' => 'hour',
            'distance_title' => 'Liters'
        ]);

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('devices', 'fuel_per_h')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('fuel_per_h');
        });
    }
}
