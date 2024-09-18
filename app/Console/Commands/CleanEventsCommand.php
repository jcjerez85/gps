<?php namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Console\ProcessManager;
use Tobuli\Entities\Event;

class CleanEventsCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'events:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Events cleaner';

    protected $date;

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

        $date =  $this->argument('date').' 00:00:00';

        do {
            $deleted = Event::where('created_at', '<', $date)->limit(10000)->delete();
            sleep(1);
        } while ($deleted > 0);

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
            array('date', InputArgument::REQUIRED, 'The date')
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
