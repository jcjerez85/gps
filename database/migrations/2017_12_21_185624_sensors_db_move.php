<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class SensorsDBMove extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sensorDbConfig = array_merge(
            config("database.connections.traccar_mysql"),
            ['database' => 'gpswox_sensors']
        );
        config()->set("database.connections.sensors_mysql", $sensorDbConfig);

        try {
            DB::connection('sensors_mysql')->getPdo();
        } catch (Exception $e) {
            return;
        }

        if (Schema::connection('traccar_mysql')->hasTable('devices'))
        {
            Artisan::call('down');

            $devices = DB::connection('traccar_mysql')->table('devices')->get();

            foreach ($devices as $device)
            {
                if (!Schema::connection('traccar_mysql')->hasTable('positions_'.$device->id))
                    continue;

                if (!Schema::connection('sensors_mysql')->hasTable('sensors_'.$device->id))
                    continue;

                DB::connection('traccar_mysql')->insert(
                    "INSERT INTO positions_{$device->id} (device_id, altitude, course, latitude, longitude, other, power, speed, time, server_time, valid, distance, protocol)
                 SELECT `positions`.`device_id`, `positions`.`altitude`, `positions`.`course`, `positions`.`latitude`, `positions`.`longitude`, `sensors`.`other`, `positions`.`power`, `positions`.`speed`, `sensors`.`time`, `positions`.`server_time`, `positions`.`valid`, 0 AS distance, `positions`.`protocol` 
                 FROM `positions_{$device->id}` AS `positions` 
                 INNER JOIN `gpswox_sensors`.`sensors_{$device->id}` AS `sensors` ON `positions`.`id` = `sensors`.`position_id` AND `positions`.`time` <> `sensors`.`time` 
                 WHERE `positions`.`valid` = ?", ['2']);

                DB::connection('sensors_mysql')->table('sensors_'.$device->id)->truncate();
            }

            Artisan::call('up');
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
