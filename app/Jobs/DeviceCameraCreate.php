<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tobuli\Entities\DeviceCamera;
use App\Events\DeviceCameraCreated;
use Tobuli\Services\FtpUserService;

class DeviceCameraCreate extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $camera;
    private $user;
    private $ftpUserService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DeviceCamera $camera, $user)
    {
        $this->queue = 'service';
        $this->camera = $camera;
        $this->user = $user;
        $this->ftpUserService = new FtpUserService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->ftpUserService->generateCameraFtpUser($this->camera);
        event(new DeviceCameraCreated($this->camera, $message));
    }
}
