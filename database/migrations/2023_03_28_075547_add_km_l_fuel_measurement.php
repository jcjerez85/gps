<?php

use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\DeviceFuelMeasurement;

class AddKmLFuelMeasurement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DeviceFuelMeasurement::updateOrCreate(['title' => 'km/l'], [
            'title' => 'km/l',
            'fuel_title' => 'liter',
            'distance_title' => 'Kilometers'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
