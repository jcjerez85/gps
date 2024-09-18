<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteDatabaseTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const RETRY_S = 60 * 5;
    const MAX_RETRY_S = 3600 * 24 * 5; // 432000

    private $table;
    private $connectionName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $table, string $connectionName)
    {
        $this->table = $table;
        $this->connectionName = $connectionName;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        try {
            if (Schema::connection($this->connectionName)->hasTable($this->table)) {
                DB::connection($this->connectionName)->table($this->table)->truncate();

                Schema::connection($this->connectionName)->dropIfExists($this->table);
            }
        } catch (\Exception $e) {
            $retryAfter = self::RETRY_S * pow($this->attempts(), 2);
            $this->release($retryAfter);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(self::MAX_RETRY_S);
    }
}
