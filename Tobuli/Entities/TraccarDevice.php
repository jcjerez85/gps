<?php namespace Tobuli\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tobuli\Relations\HasManyTable;
use Tobuli\Services\DatabaseService;

class TraccarDevice extends AbstractEntity {
	protected $table = 'traccar_devices';

    protected $fillable = array(
        'database_id',
        'name',
        'uniqueId',
        'latestPosition_id',
        'lastValidLatitude',
        'lastValidLongitude',
        'device_time',
        'server_time',
        'ack_time',
        'time',
        'speed',
        'other',
        'altitude',
        'power',
        'course',
        'address',
        'protocol',
        'latest_positions'
    );

    public $timestamps = false;

    public function positions()
    {
        $instance = new TraccarPosition();

        if ($connection = $this->getDatabaseName())
            $instance->setConnection($connection);

        return new HasManyTable($instance->newQuery(), $this);
    }

    public function getDatabaseName()
    {
        return DatabaseService::instance()->getDatabaseName($this->database_id);
    }

    public function copyTo($database_id)
    {
        $from = DatabaseService::instance()->getDatabaseConfig($this->database_id);
        $to   = DatabaseService::instance()->getDatabaseConfig($database_id);
        $table = "positions_{$this->id}";

        $command = implode(' | ', [
            "mysqldump -h {$from['host']} -u {$from['username']} -p{$from['password']} --port={$from['port']} --insert-ignore --skip-add-drop-table {$from['database']} $table",
            "mysql -h {$to['host']} -u {$to['username']} -p{$to['password']} --port={$to['port']} {$to['database']}"
        ]);

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(0);
        $process->run();
        $process->wait();

        if ( ! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $connection = $this->getDatabaseName();

        if (Schema::connection($connection)->hasTable($table)) {
            DB::connection($connection)->table($table)->truncate();
            Schema::connection($connection)->dropIfExists($table);
        }

        $this->database_id = $database_id;
        $this->save();
    }

    public function getLastConnectionAttribute()
    {
        $timestamp = $this->lastConnectTimestamp;

        if ( ! $timestamp)
            return null;

        return Carbon::createFromTimestamp($timestamp);
    }

    public function getLastConnectTimestampAttribute() {
        return strtotime($this->server_time);
    }
}
