<?php
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendConfigurationCommands extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $smsManager;
    private $phoneNumber;
    private $commands;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($smsManager, $phoneNumber, $commands)
    {
        $this->queue = 'send';
        $this->smsManager = $smsManager;
        $this->phoneNumber = $phoneNumber;
        $this->commands = $commands;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $status = 1;

        try {
            foreach ($this->commands as $command) {
                $this->smsManager->send($this->phoneNumber, $command);

                sleep(config('tobuli.device_configuration.delay', 5));
            }
        } catch (\Exception $e) {
            $status = 0;
        }

        //@TODO: send status to front?
    }
}
