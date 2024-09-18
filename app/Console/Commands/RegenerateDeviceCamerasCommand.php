<?php namespace App\Console\Commands;

set_time_limit(0);

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceCamera;
use Tobuli\Entities\File\DeviceCameraMedia;
use Tobuli\Services\DeviceService;
use Tobuli\Services\FtpUserService;

class RegenerateDeviceCamerasCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'device_cameras:regenerate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Regenerate device cameras ftp users';

    /**
     * @var FtpUserService
     */
    protected $ftpUserService;

    /**
     * @var DeviceCameraMedia
     */
    protected $deviceMedia;

    public function __construct(FtpUserService $ftpUserService)
    {
        parent::__construct();

        $this->ftpUserService = $ftpUserService;
        $this->deviceMedia = new DeviceCameraMedia();
    }


    /**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        DeviceCamera::chunk(100, function($cameras) {
            foreach ($cameras as $camera) {
                $path = $this->deviceMedia->getDirectory($camera);
                $this->ftpUserService->removeFtpUser($camera->ftp_username);
                $result = $this->ftpUserService->createFtpUser($camera->ftp_username, $camera->ftp_password, $path);

                if (empty($result['error'])) {
                    $this->info($camera->ftp_username . ' OK');
                } else {
                    $this->error($camera->ftp_username . ' ' . $result['error']);
                }
            }
        });
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
