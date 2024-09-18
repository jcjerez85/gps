<?php

namespace Tobuli\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Database;


class DatabaseService
{
    const DEFAULT_CONNECTION = 'traccar_mysql';

    public function __construct()
    {

    }

    public static function instance() : self
    {
        return new static();
    }

    public static function loadDatabaseConfig()
    {
        Cache::store('array')->remember('device.position.databases', 60, function() {
            $databases = Database::all();

            foreach ($databases as $database)
                config()->set("database.connections." . self::toName($database->id), $database->toArray());

            return $databases;
        });
    }

    public static function toName($database_id) : string
    {
        return "database{$database_id}";
    }

    public function getConnection($database_id) : Connection
    {
        return DB::connection($this->getDatabaseName($database_id));
    }

    public function getDatabaseName($database_id) : string
    {
        return $database_id && $this->getDatabaseConfig($database_id)
            ? self::toName($database_id)
            : self::DEFAULT_CONNECTION;
    }

    public function getDatabaseConfig($database_id) : array
    {
        if (empty($database_id))
            return config("database.connections." . self::DEFAULT_CONNECTION);

        self::loadDatabaseConfig();

        return config("database.connections." . self::toName($database_id));
    }

    public function getDatabases() : Collection
    {
        $config = config("database.connections." . self::DEFAULT_CONNECTION);
        $default = new Database();
        $default->id = 0;
        $default->active = false;
        $default->driver = $config['driver'];
        $default->host = $config['host'];
        $default->port = $config['port'];
        $default->username = $config['username'];
        $default->password = $config['password'];
        $default->database = $config['database'];


        return Database::all()->prepend($default);
    }

    public function getActiveDatabaseId()
    {
        $actives = Cache::store('array')->remember('device.position.active_databases', 60, function() {
            return Database::where('active', 1)->get();
        });

        if ($actives->isEmpty())
            return null;

        return $actives->random()->first()->id;
    }

    public function getDatabaseSizes($database_id)
    {
        $connection = $this->getConnection($database_id);

        try {
            $sizes = $connection
                ->table('space_usage')
                ->whereIn('id', ['total', 'free', 'reserved'])
                ->get()
                ->sortBy('id')
                ->map(function($row) {
                    return substr(strtoupper($row->id), 0, 1) . " " . formatBytes($row->value) . " " . $row->updated_at;
                });

            return $sizes->toArray();
        } catch (\Exception $e) {
            return null;
        }
    }
}
