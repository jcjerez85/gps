<?php namespace App\Console\Commands\Tracker;

use Illuminate\Console\Command;
use Tobuli\Helpers\Tracker;

class ConfigResetCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'tracker:config:reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Tracker service config reset generation.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
        $tracker = new Tracker();

        try {
            $tracker->config()->reset();

            $this->line('Ok');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
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
