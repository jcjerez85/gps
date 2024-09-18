<?php namespace App\Console\Commands;

set_time_limit(0);

use Illuminate\Console\Command;

use App\Console\ProcessManager;
use Symfony\Component\Console\Input\InputOption;
use Tobuli\Helpers\Backup;

class BackupMysqlCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'backup:mysql';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
        $this->processManager = new ProcessManager($this->name, $timeout = 604800, $limit = 1);

        if ( ! $this->processManager->canProcess()) {
            echo "Cant process \n";
            return;
        }

        $settings = settings('backups');
        $backup = new Backup($settings);

        if ($this->option('force')) {
            $backup->force();
        } else {
            $backup->auto();
        }

        $this->line("Job done\n");
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
        return array(
            array('force', null, InputOption::VALUE_OPTIONAL, 'Force option.', null),
        );
	}
}
