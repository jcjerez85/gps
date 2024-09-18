<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\Device;

class AlterDevicesTableFuelPerKm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            DB::statement('ALTER TABLE `devices` CHANGE `fuel_per_km` `fuel_per_km` DECIMAL(10, 4);');
        });

        $this->recalculateFuelPerKm();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            DB::statement('ALTER TABLE `devices` CHANGE `fuel_per_km` `fuel_per_km` DECIMAL(8, 2);');
        });

        $this->recalculateFuelPerKm();
    }

    /**
     * Recalculates fuel_per_km field for all devices
     *
     * @return void
     */
    private function recalculateFuelPerKm()
    {
        Device::whereNotNull('fuel_quantity')
            ->where('fuel_quantity', '>', 0)
            ->chunk(500, function($devices) {
                beginTransaction();

                try {
                    foreach ($devices as $device) {
                        $device->update([
                            'fuel_per_km' => convertFuelConsumption($device->fuel_measurement_id, $device->fuel_quantity),
                        ]);
                    }
                } catch (\Exception $e) {
                    rollbackTransaction();

                    throw $e;
                }

                commitTransaction();
            });
    }
}
