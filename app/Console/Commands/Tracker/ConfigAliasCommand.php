<?php namespace App\Console\Commands\Tracker;


class ConfigAliasCommand extends ConfigCommand {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:config';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
}
