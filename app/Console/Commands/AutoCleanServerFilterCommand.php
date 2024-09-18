<?php namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Console\ProcessManager;
use Symfony\Component\Console\Input\InputArgument;
use Tobuli\Entities\TraccarDevice;

class AutoCleanServerFilterCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:autocleanfilter';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';


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
        $this->processManager = new ProcessManager($this->name, $timeout = 3600, $limit = 1);

        if ( ! $this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

        $since = Carbon::now()->subDays($this->argument('offline_days'))->format('Y-m-d') . ' 00:00:00';
        $date  = Carbon::now()->subDays($this->argument('leave_days'))->format('Y-m-d') . ' 00:00:00';

        $devices = TraccarDevice::whereNotNull('time')
            ->where('time', '>', $since)
            ->orderBy('id', 'asc')
            ->get();

        $all = count($devices);
        $i = 1;

        foreach ($devices as $device) {
            try {
                $devices->positions()->where('time', '<', $date)->delete();
            } catch (\Exception $e) {

            }

            $this->line("CLEAN TABLES ({$i}/{$all})\n");
            $i++;
        }

        $this->line("Job done[OK]\n");

        return 0;
	}

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('offline_days', InputArgument::REQUIRED, 'Days devices is offline'),
            array('leave_days', InputArgument::REQUIRED, 'Days to leave the data')
        );
    }
}
