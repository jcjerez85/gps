<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tobuli\Entities\TrackerPort;
use Tobuli\Entities\User;
use Tobuli\Helpers\Tracker;

class TrackerRestart extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user = null)
    {
        $this->queue = 'tracker';

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tracker = new Tracker();

        if ($this->user)
            $tracker->actor($this->user);

        $tracker->restart();
    }
}
