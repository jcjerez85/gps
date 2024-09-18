<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\Device;

class ConvertDevicesFuelMeasurements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Device::where('fuel_measurement_id', 1)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity * 100'),
        ]);

        Device::where('fuel_measurement_id', 3)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity'),
        ]);

        Device::where('fuel_measurement_id', 4)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Device::where('fuel_measurement_id', 1)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity * 100'),
        ]);

        Device::where('fuel_measurement_id', 3)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity'),
        ]);

        Device::where('fuel_measurement_id', 4)->update([
            'fuel_quantity' => DB::raw('1 / fuel_quantity'),
        ]);
    }
}
