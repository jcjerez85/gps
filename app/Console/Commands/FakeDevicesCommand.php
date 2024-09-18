<?php namespace App\Console\Commands;

set_time_limit(0);

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use Tobuli\Entities\Device;
use Tobuli\Services\DeviceService;

class FakeDevicesCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'devices:fake';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fake devices';

    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        parent::__construct();

        $this->deviceService = $deviceService;
    }


    /**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        if ($this->option('fill')) {
            $this->fillDevices();
        } else {
            $this->updateDevices();
        }
	}

	protected function fillDevices()
    {
        $limit = $this->ask("Limit:");

        $lastID = Device::latest()->orderBy('id', 'desc')->first()->id ?? 0;

        $bar = $this->output->createProgressBar($limit);

        try {
            beginTransaction();
            for ($i = 1; $i <= $limit; $i++) {
                if ($i % 500 == 0) {
                    commitTransaction();
                    beginTransaction();
                }
                $this->createDevice($lastID + $i);
                $bar->advance();
            }
            commitTransaction();
        } catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }

        $bar->finish();
    }

    protected function updateDevices()
    {
        $processManager = new ProcessManager($this->name, $timeout = 110, $limit = 1);

        if ( ! $processManager->canProcess())
        {
            echo "Cant process \n";
            return false;
        }

        Device::with(['traccar'])->protocol('demo')->chunk(500, function($devices){
            beginTransaction();
            try {
                foreach ($devices as $device) {
                    $this->updateDevicePosition($device);
                }
            } catch (\Exception $e) {
                rollbackTransaction();
                throw $e;
            }
            commitTransaction();
        });
    }

	protected function createDevice($index)
    {
        $device = $this->deviceService->create([
            'name' => "Device $index",
            'imei' => "D" . str_pad($index, 10, "0", STR_PAD_LEFT)
        ]);

        $this->updateDevicePosition($device);
    }

    protected function updateDevicePosition($device)
    {
        if (empty($device->traccar->lastValidLatitude) && empty($device->traccar->lastValidLongitude)) {
            $point = [
                rand(-5500000, 7500000) * 0.00001,
                rand(-18000000, 18000000) * 0.00001,
            ];
        } else {
            $point = $this->generateRandomPoint([
                $device->traccar->lastValidLatitude,
                $device->traccar->lastValidLongitude
            ], 4);
        }

        $device->traccar->protocol = 'demo';

        $device->traccar->lastValidLatitude  = $point[0];
        $device->traccar->lastValidLongitude = $point[1];

        $time = date('Y-m-d H:i:s');
        $device->traccar->device_time = $time;
        $device->traccar->time = $time;
        $device->traccar->server_time = $time;

        $device->speed = rand(0, 90);

        if ($device->speed > $device->min_moving_speed) {
            $device->traccar->moved_at = $time;
        } else {
            $device->traccar->stoped_at = $time;
        }

        $device->traccar->save();
    }

    protected function generateRandomPoint($centre, $radius) {
        $radius_earth = 3959; //miles

        //Pick random distance within $distance;
        $distance = lcg_value()*$radius;

        //Convert degrees to radians.
        $centre_rads = array_map( 'deg2rad', $centre );

        //First suppose our point is the north pole.
        //Find a random point $distance miles away
        $lat_rads = (pi()/2) -  $distance/$radius_earth;
        $lng_rads = lcg_value()*2*pi();


        //($lat_rads,$lng_rads) is a point on the circle which is
        //$distance miles from the north pole. Convert to Cartesian
        $x1 = cos( $lat_rads ) * sin( $lng_rads );
        $y1 = cos( $lat_rads ) * cos( $lng_rads );
        $z1 = sin( $lat_rads );


        //Rotate that sphere so that the north pole is now at $centre.

        //Rotate in x axis by $rot = (pi()/2) - $centre_rads[0];
        $rot = (pi()/2) - $centre_rads[0];
        $x2 = $x1;
        $y2 = $y1 * cos( $rot ) + $z1 * sin( $rot );
        $z2 = -$y1 * sin( $rot ) + $z1 * cos( $rot );

        //Rotate in z axis by $rot = $centre_rads[1]
        $rot = $centre_rads[1];
        $x3 = $x2 * cos( $rot ) + $y2 * sin( $rot );
        $y3 = -$x2 * sin( $rot ) + $y2 * cos( $rot );
        $z3 = $z2;


        //Finally convert this point to polar co-ords
        $lng_rads = atan2( $x3, $y3 );
        $lat_rads = asin( $z3 );

        return array_map( 'rad2deg', array( $lat_rads, $lng_rads ) );
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
        return array(
            array('fill', null, InputOption::VALUE_OPTIONAL, 'Fill option.', null),
        );
	}
}
