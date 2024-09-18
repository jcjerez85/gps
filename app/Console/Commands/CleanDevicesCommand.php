<?php namespace App\Console\Commands;

set_time_limit(0);

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Console\ProcessManager;
use Tobuli\Entities\TraccarDevice;

class CleanDevicesCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'devices:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Devices positions cleaner';

    protected $type;
    protected $value;

    protected $i;
    protected $all;

	public function __construct()
	{
		parent::__construct();

        $this->all = 0;
        $this->i = 0;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $this->processManager = new ProcessManager($this->name, $timeout = 3600, $limit = 1);

        if ( ! $this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

		$this->type = $this->argument('type');
        $this->value = $this->argument('value');

		$this->all = TraccarDevice::count();

        TraccarDevice::orderBy('id', 'asc')->chunk(500, function($devices){
            foreach ($devices as $device)
            {
                $this->i++;

                $date = $this->getDate($device);

                try {
                    $query = $device->positions()->where(function($q) use ($date){
                        $q->whereNull('time');

                        if ($date)
                            $q->orWhere('time', '<', $date);
                    })->limit(10000);

                    do {
                        $deleted = (clone $query)->delete();
                    } while ($deleted > 0);

                    $this->line("CLEAN TABLES ({$this->i}/{$this->all}) Device {$device->id} {$date}");
                } catch (\Exception $e) {
                    $this->error("CLEAN TABLES ({$this->i}/{$this->all}) Device {$device->id} " . $e->getMessage());
                }
            }
        });

		$this->line("Job done[OK]\n");
	}

	protected function getDate($device)
    {
        $date = null;

        switch ($this->type) {
            case 'date':
                $date = Carbon::parse($this->value);
                break;

            case 'days':
                if ($lastConnection = $device->lastConnection)
                {
                    $date = Carbon::parse($lastConnection);

                    if ($date->gt(Carbon::now()))
                        $date= Carbon::now();

                    $date = $date->subDays($this->value);
                }

                break;
        }

        return $date;
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('type', InputArgument::REQUIRED, 'Type [date, days]'),
            array('value', InputArgument::REQUIRED, 'Value [yyyy-mm-dd, days]')
		);
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
