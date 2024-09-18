<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tobuli\Entities\Device;


class CalcVirtualOdometerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'virtual_odometer:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = date('Y-m-d H:i:s', settings('alerts_last_check'));
        settings('alerts_last_check', time());

        $devices = Device::connectedAfter($time)
            ->whereHas('sensors', function($query){
                $query->where('type', 'odometer');
                $query->where('odometer_value_by', 'virtual_odometer');
            })
            ->with(['sensors' => function($query) {
                $query->where('type', 'odometer');
                $query->where('odometer_value_by', 'virtual_odometer');
            }])
            ->get();

        foreach ($devices as $device) {
            $this->process($device, $time);
        }

        $this->line("DONE");
    }

    protected function process($device, $time)
    {
        static $select = ['id', 'latitude', 'longitude', 'time', 'distance'];

        $positions = $device->positions()
            ->select($select)
            ->union(
                $device->positions()
                    ->select($select)
                    ->where('server_time', '<=', $time)
                    ->where('valid', '>', 0)
                    ->orderliness()
                    ->limit(1)
            )
            ->where('server_time', '>', $time)
            ->where('valid', '>', 0)
            ->orderliness()
            ->get();

        $previous = $positions->shift();

        foreach ($positions as &$position) {

            $distance = getDistance($position->latitude, $position->longitude, $previous->latitude, $previous->longitude);

            if (round($distance, 5) != round($position->distance, 5)) {
                $position->distance = $distance;

                $device->positions()->whereId($position->id)->update(['distance' => $distance]);
            }

            $previous = $position;
        }

        $distance = $positions->sum('distance');

        if (!$distance)
            return;

        foreach ($device->sensors as $sensor) {
            $sensor->increment('odometer_value', $distance);
        }

    }

    protected function process2(Device $device, $time)
    {
        $posTable = $device->positions()->toBase()->from;
        $conn = $device->positions()->getConnection();

        $conn->statement("
                UPDATE $posTable pos
            INNER JOIN (  SELECT IFNULL(DEGREES(ACOS( 
                                           COS(RADIANS(p.latitude)) 
                                         * COS(RADIANS(prev.latitude)) 
                                         * COS(RADIANS(prev.longitude) - RADIANS(p.longitude))       
                                         + SIN(RADIANS(p.latitude)) 
                                         * SIN(RADIANS(prev.latitude))
                                       )) * 111.045, 0
                                 ) AS distance,
                                 p.id
                            FROM $posTable p
                       LEFT JOIN $posTable prev ON prev.id = p.id - 1
                           WHERE p.server_time > '$time' AND p.valid > 0
                        ORDER BY p.id DESC, p.time DESC
                       ) t ON t.id = pos.id
                   SET pos.distance = t.distance
                 WHERE ROUND(t.distance, 5) != ROUND(pos.distance, 5)");

        \DB::statement("
            UPDATE device_sensors d
               SET d.odometer_value = d.odometer_value + (SELECT SUM(p.distance) FROM $posTable p WHERE p.server_time > '$time' AND p.valid > 0)
             WHERE d.device_id = $device->id");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}