<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tobuli\Entities\CommandSchedule;
use Tobuli\Services\Commands\SendCommandService;


class RunCommandScheduleJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $commandSchedule;
    private $sendCommandService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CommandSchedule $schedule)
    {
        $this->commandSchedule = $schedule;
        $this->sendCommandService = new SendCommandService($schedule);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $devices = $this->commandSchedule->devices;
        $user    = $this->commandSchedule->user;

        setActingUser($user);

        if ($this->commandSchedule->getAttribute('connection') == SendCommandService::CONNECTION_GPRS) {
            $data = $this->commandSchedule->parameters;
            $data['type'] = $this->commandSchedule->command;

            $this->sendCommandService->gprs($devices, $data, $user);
        } else {
            $this->sendCommandService->sms($devices, $this->commandSchedule->message, $user);
        }
    }
}
