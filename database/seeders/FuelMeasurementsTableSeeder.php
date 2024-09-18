<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Tobuli\Entities\DeviceFuelMeasurement;

class FuelMeasurementsTableSeeder extends Seeder
{
    public function run()
    {
        DeviceFuelMeasurement::updateOrCreate(['title' => 'l/100km'], [
            'title' => 'l/100km',
            'fuel_title' => 'kilometer',
            'distance_title' => 'Liters'
        ]);

        DeviceFuelMeasurement::updateOrCreate(['title' => 'MPG'], [
            'title' => 'MPG',
            'fuel_title' => 'gallon',
            'distance_title' => 'Miles'
        ]);

        DeviceFuelMeasurement::updateOrCreate(['title' => 'kWh/km'], [
            'title' => 'kWh/km',
            'fuel_title' => 'kilometer',
            'distance_title' => 'Kilowatt-hours'
        ]);

        DeviceFuelMeasurement::updateOrCreate(['title' => 'l/h'], [
            'title' => 'l/h',
            'fuel_title' => 'hour',
            'distance_title' => 'Liters'
        ]);

        DeviceFuelMeasurement::updateOrCreate(['title' => 'km/l'], [
            'title' => 'km/l',
            'fuel_title' => 'liter',
            'distance_title' => 'Kilometers'
        ]);
    }

}