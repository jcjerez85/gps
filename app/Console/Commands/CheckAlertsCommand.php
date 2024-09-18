<?php namespace App\Console\Commands;

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Event;
use Tobuli\Entities\Alert;
use Tobuli\Entities\SendQueue;
use Tobuli\Helpers\Alerts\Checker;
use Tobuli\Services\EventWriteService;

class CheckAlertsCommand extends Command
{
    /**
     * @var EventWriteService
     */
    private $eventWriteService;

    private $events = [];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'alerts:check';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for stop duration alerts and add them';
    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->eventWriteService = new EventWriteService();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processManager = new ProcessManager($this->name, $timeout = 300, $limit = 1);

        if ( ! $this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

        $this->call('virtual_odometer:calc');

        $start = microtime(true);

        Alert::with('user', 'zones')
            ->withCount('devices')
            ->checkByTime()
            ->active()
            ->chunk(100, function($alerts) {
                $devices_count = $alerts->sum('devices_count');
                $this->line('devices_count: ' . $devices_count);

                if (3000 > $devices_count) {
                    $alerts->load(['devices', 'devices.traccar']);

                    foreach ($alerts as $alert) {
                        if ($alert->type == 'distance') {
                            $alert->devices->load(['sensors']);
                        }

                        $this->checker($alert->devices, $alert);
                    }
                } else {
                    foreach ($alerts as $alert) {
                        $query = $alert->devices()->unexpired()->with('traccar');

                        switch ($alert->type) {
                            case 'offline_duration':
                                $query->offline(intval($alert->offline_duration));
                                break;
                            case 'distance':
                                $query->with('sensors');
                                break;
                        }

                        $query->chunk(3000, function($devices) use ($alert) {
                            $this->checker($devices, $alert);
                        });
                    }
                }
            });

        $this->line('Time ' . (microtime(true) - $start));

        $this->writeEvents();

        echo "DONE\n";

        return 0;
    }

    protected function checker($devices, $alert)
    {
        foreach ($devices as $device)
        {
            $checker = new Checker($device, [$alert]);

            $position = $device->positionTraccar();
            if ($position)
                $position->time = date('Y-m-d H:i:s');

            $events = $checker->check($position);

            $this->addEvents($events);
        }
    }

    protected function addEvents($events)
    {
        if ( ! $events)
            return;

        $this->events = array_merge($this->events, $events);

        if (count($this->events) > 100)
            $this->writeEvents();
    }

    protected function writeEvents()
    {
        $this->eventWriteService->write($this->events);
        $this->events = [];
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