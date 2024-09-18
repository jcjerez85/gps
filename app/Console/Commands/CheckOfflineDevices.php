<?php

namespace App\Console\Commands;

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Tobuli\Entities\TraccarDevice;

class CheckOfflineDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:check_offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks devices which were not updated for a certain period';

    /**
     * @var ProcessManager
     */
    private $processManager;

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->processManager = new ProcessManager($this->name, 60, 1);

        if ( ! $this->processManager->canProcess()) {
            $this->error('Can\'t process');
            return -1;
        }

        while ($this->processManager->canContinue()) {
            $offlineDuration = settings('main_settings.default_object_online_timeout') * 60;

            $onlineFrom = \Carbon::now()->subSeconds($offlineDuration);
            $now = \Carbon::now();

            TraccarDevice::where('server_time', '<', $onlineFrom)
                ->whereRaw("updated_at < DATE_ADD(server_time, INTERVAL $offlineDuration SECOND)")
                ->update(['updated_at' => $now]);

            sleep(30);
        }

        return 0;
    }
}
