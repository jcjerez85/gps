<?php namespace App\Console\Commands\Socket;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;


class ServiceCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
    protected $name = 'socket:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start socket service';


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
        $process = Process::fromShellCommandline("pm2 delete socket");
        $process->run();

        while ($process->isRunning()) {}

        $process = Process::fromShellCommandline("pm2 start socket.config.js");
        $process->run();

        while ($process->isRunning()) {}

        if ( ! $process->isSuccessful())
            $this->error($process->getErrorOutput());

        $this->line($process->getOutput());
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
