<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;

use App\Console\ProcessManager;
use Tobuli\Services\DatabaseService;

class OptimizeServerDBCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:dboptimize';

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

        if (!$this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

        DatabaseService::loadDatabaseConfig();

        $connections = Arr::where(config("database.connections"), function($config, $connection){
            return $config['driver'] == 'mysql';
        });

        foreach ($connections as $connection => $config) {
            $this->line("OPTIMIZE Database {$config['host']} {$config['database']}\n");
            $tables = DB::connection($connection)->select('SHOW TABLES');
            $column = "Tables_in_{$config['database']}";

            $bar = $this->output->createProgressBar(count($tables));

            foreach ($tables as $table) {
                DB::connection($connection)->statement("OPTIMIZE TABLE {$table->{$column}};");
                $bar->advance();
            }

            $bar->finish();
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
