<?php namespace App\Console\Commands;

use Tobuli\Helpers\TrackerConfig;
use Illuminate\Console\Command;

class CompressLogsCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'logs:compress';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates server database and configuration to the newest version.';

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
		# Traccar
        $config = config('tracker');
        $path = pathinfo($config['logger.file'], PATHINFO_DIRNAME ) . '/*.log.*';

		$files = glob($path);
		foreach ($files as $file) {
			$arr = explode('.', $file);
			$ex = end($arr);
			if ($ex == 'gz' || $ex == date('Ymd'))
				continue;

			@exec('gzip '.$file);
		}

		# HTTPD access
		$files = glob('/var/log/httpd/access_log-*');
		foreach ($files as $file) {
			@exec('gzip '.$file);
		}

		# HTTPD error
		$files = glob('/var/log/httpd/error_log-*');
		foreach ($files as $file) {
			@exec('gzip '.$file);
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
