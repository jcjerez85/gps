<?php namespace App\Console\Commands;

set_time_limit(0);


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Console\ProcessManager;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarDevice;
use Tobuli\Services\DatabaseService;

class CopyDevicesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'positions:copy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Devices positions copy';

    /**
     * @var DatabaseService
     */
    protected $dbService;


    public function __construct()
	{
		parent::__construct();

        $this->dbService = new DatabaseService();
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

        $query = Device::query();
        $this->displayFilterCount($query);

        $this->filterId($query);
        $this->displayFilterCount($query);

        $this->filterConnectionTime($query);
        $this->displayFilterCount($query);

        $this->databaseSizes();

        $this->filterPositionDatabase($query);
        $this->displayFilterCount($query);

        $database_id = $this->askDatabaseCopyTo();

        $this->line("Copy to: " . $this->dbService->getDatabaseConfig($database_id)['host']);

        $this->filterCurrentDatabase($query, $database_id);
        $this->displayFilterCount($query);

        $this->filterLimit($query);

        $devices = $query->get();

        $bar = $this->output->createProgressBar(count($devices));

        foreach ($devices as $device) {
            $this->performTask($device, $database_id);

            $bar->advance();
        }

        $bar->finish();

		$this->line("Job done[OK]\n");

        return 0;
	}

    protected function performTask($device, $database_id) {
        try {
            if (!$device->traccar) {
                $this->error("Device '{$device->imei}' without traccar instance");
                return;
            }

            $device->traccar->copyTo($database_id);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function databaseSizes()
    {
        foreach ($this->dbService->getDatabases() as $database) {
            $sizes = $this->dbService->getDatabaseSizes($database->id);

            if ($sizes) {
                $sizes = "\n" . implode("\n", $sizes);
            }

            $this->line("{$database->host} $sizes");
        }
    }

    protected function askDatabaseCopyTo()
    {
        $choises = [];

        foreach ($this->dbService->getDatabases() as $database) {
            if ($database->id)
                $count = TraccarDevice::where('database_id', $database->id)->count();
            else
                $count = TraccarDevice::whereNull('database_id')->count();

            $choises[$database->id] = "{$database->host} (Count: $count)";
        }

        $choise = $this->choice("Copy to database:", $choises, 0);

        $database_id = array_search($choise, $choises);

        return $database_id ? $database_id : null;
    }

    protected function filterId(&$query) {
        $do = $this->choice('Filter by id?', ['No', 'Yes']);

        if (strtolower($do) == 'no')
            return;

        $id = $this->ask("Filter ID from:");

        if ($id)
            $query->where('id', '>=', $id);

        $id = $this->ask("Filter ID to:");

        if ($id)
            $query->where('id', '<=', $id);
    }

    protected function filterConnectionTime(&$query) {
        $do = $this->choice('Filter by connection time?', ['No', 'Yes']);

        if (strtolower($do) == 'no')
            return;

        $time = $this->ask("Filter last connection time before (Y-m-d H:i:s):");

        if ($time)
            $query->connectedBefore($time);

        $time = $this->ask("Filter last connection time after (Y-m-d H:i:s):");

        if ($time)
            $query->connectedAfter($time);
    }

    protected function filterPositionDatabase(&$query) {
        $do = $this->choice('Filter by position database?', ['No', 'Yes']);

        if (strtolower($do) == 'no')
            return;

        $databases = $this->dbService->getDatabases();

        $choises = [];

        foreach ($databases as $database) {
            if ($database->id)
                $count = TraccarDevice::where('database_id', $database->id)->count();
            else
                $count = TraccarDevice::whereNull('database_id')->count();

            $choises[$database->id] = "{$database->host} (Count: $count)";
        }

        $choise = $this->choice("Filter position database:", $choises, 0);

        $database_id = array_search($choise, $choises);

        $query->traccarJoin();

        if ($database_id)
            $query->where('traccar_devices.database_id', $database_id);
        else
            $query->whereNull('traccar_devices.database_id');
    }

    protected function filterLimit(&$query) {
        $do = $this->choice('Filter by limit?', ['No', 'Yes']);

        if (strtolower($do) == 'no')
            return;

        $limit = $this->ask("Filter limit:");

        if ($limit)
            $query->limit($limit);
    }

    protected function filterCurrentDatabase(&$query, $database_id)
    {
        $hasDatabaseFilter = Arr::first($query->getQuery()->wheres, function($where){
            return Arr::get($where, 'column') == 'traccar_devices.database_id';
        });

        if ($hasDatabaseFilter)
            return;

        $query->traccarJoin();

        if (empty($database_id)) {
            $query->whereNotNull('traccar_devices.database_id');
        } else {
            $query->where('traccar_devices.database_id', '!=', $database_id);
        }
    }

    protected function displayFilterCount($query) {
        $this->info("Devices selected: " . (clone $query)->count());
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
