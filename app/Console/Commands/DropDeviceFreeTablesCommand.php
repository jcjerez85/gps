<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Tobuli\Entities\Database;
use Tobuli\Entities\TraccarDevice;
use Tobuli\Services\DatabaseService;

class DropDeviceFreeTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'positions:free_tables {action=view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops tables which do not have relation to existing device.';

    /**
     * @var DatabaseService
     */
    protected $dbService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $drop = $this->argument('action') === 'drop';

        $databases = $this->dbService->getDatabases();

        /** @var Database $database */
        foreach ($databases as $database) {
            $connection = $this->dbService->getConnection($database->id);

            $host = implode(" ", Arr::only($connection->getConfig(), ['host', 'database']));

            $tables = $connection->getDoctrineSchemaManager()->listTableNames();

            foreach ($tables as $table) {
                preg_match('/^positions_(\d+)$/', $table, $matches);

                $deviceId = $matches[1] ?? null;

                if (empty($deviceId)) {
                    continue;
                }

                $device = TraccarDevice::where('id', $deviceId)->first();

                $dropReason = $this->getDropReason($device, $database);

                if ($dropReason === null) {
                    continue;
                }

                $text = "$database->id $host $table";

                if ($drop) {
                    $connection->table($table)->truncate();
                    $connection->getSchemaBuilder()->dropIfExists($table);

                    $text .= ' DROPPED';
                }

                $this->line($text . '. ' . $dropReason);
            }
        }
    }

    protected function getDropReason(?TraccarDevice $device, Database $database): ?string
    {
        if  ($device === null) {
            return 'ID not found';
        }

        if ((int)$device->database_id !== $database->id) {
            return 'Database mismatch. Device database ID: ' . $device->database_id;
        }

        return null;
    }
}
