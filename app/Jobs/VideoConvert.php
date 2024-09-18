<?php

namespace App\Jobs;

use App\Events\MediaConvertedEvent;
use App\Events\MediaConvertFail;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;

class VideoConvert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $tries = 1;

    public $timeout = 300;

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $target;

    /**
     * @var Device|null
     */
    public $device;

    /**
     * @var User|null
     */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($source, Device $device = null, User $user = null)
    {
        $this->source = $source;

        $this->user = $user;

        $this->device = $device;

        $this->setTarget($this->source);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists($this->source)) {
            $this->eventFail(trans('front.source_file_not_found'));
            return;
        }

        if (file_exists($this->target)) {
            $this->eventFail(trans('front.source_file_already_converted'));
            return;
        }

        try {
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($this->source);

            $video->save((new X264())->setKiloBitrate(500), $this->target);

            touch($this->target, filemtime($this->source));
            @unlink($this->source);

            $this->eventSuccess();
        } catch (\Exception $e) {
            @unlink($this->target);

            $this->eventFail($e->getMessage());

            throw $e;
        }
    }

    protected function setTarget($source)
    {
        $this->target = $source . '.mp4';
    }

    protected function eventSuccess()
    {
        event(new MediaConvertedEvent($this->target, $this->device->id, $this->user));
    }

    protected function eventFail($message)
    {
        event(new MediaConvertFail($message, $this->source, $this->device->id, $this->user));
    }
}
