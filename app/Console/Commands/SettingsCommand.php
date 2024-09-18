<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class SettingsCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'settings:set {key} {value}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set settings';

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
        $key = $this->argument('key');
        $value = $this->argument('value');

        $json = json_decode($value, true);

        if ( ! is_null($json)) {
            $value = $json;
        }

        if (is_null($json) && Str::contains($value, '{'))
        {
            $this->error("Wrong value format");
            return;
        }

        settings($key, $value);

		$this->line("OK");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('key', InputArgument::REQUIRED, 'Key'),
            array('value', InputArgument::REQUIRED, 'Value')
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
