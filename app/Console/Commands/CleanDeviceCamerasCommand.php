<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use CustomFacades\Repositories\DeviceCameraRepo;
use Tobuli\Entities\File\DeviceCameraMedia;
use Illuminate\Support\Facades\File;

class CleanDeviceCamerasCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'camera:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean device cameras storage';

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
        try {
            $images = DeviceCameraMedia::olderThan(settings('main_settings.device_cameras_days'));

            foreach ($images as $image) {
                $path = $image->path;

                if (File::exists($path)) {
                    if (!File::delete($path)) {
                        $this->line('Couldn\'t delete: '.$path);
                    }
                }
            }

            $this->line('Ok');
        } catch(\Exception $e) {

        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}
