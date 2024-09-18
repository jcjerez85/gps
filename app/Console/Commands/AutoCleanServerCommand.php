<?php namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Console\ProcessManager;

class AutoCleanServerCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:autoclean';

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

        $settings = settings('db_clear');

        if ( ! (isset($settings['status']) && $settings['status'] && $settings['days'] > 0) ) {
            $this->line("Auto cleanup disabled.");
            return -1;
        }

        $date = Carbon::now()->subDays($settings['days']);
        $diff = $date->diffInDays( Carbon::now(), false);
        $min  = config('tobuli.min_database_clear_days');

        if ( $diff < $min )
        {
            $this->line("Days to keep not reached: min - $min, current - $diff.\n");
            return -1;
        }

        if (isset($settings['from']) && $settings['from'] == 'last_connection') {
            $this->call('devices:clean', [
                'type' => 'days',
                'value' => $settings['days']
            ]);
        } else {
            $this->call('devices:clean', [
                'type' => 'date',
                'value' => $date->format('Y-m-d')
            ]);
        }

        $this->call('server:reportlogclean', [
            'date' => $date->format('Y-m-d')
        ]);

        $this->call('events:clean', [
            'date' => $date->format('Y-m-d')
        ]);

        return 0;
	}
}
