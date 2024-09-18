<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Tobuli\Services\PermissionService;
use Tobuli\Services\UserService;

class SplitDevicesCommand extends Command {

    protected $userService;

    protected $permissionService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:split {limit=2000}';


	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Devices split per users';

	public function __construct()
	{
		parent::__construct();

        $this->userService = new UserService();
        $this->permissionService = new PermissionService();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
	    $limit = $this->argument('limit');
        $total = Device::count();

        $needUsers = ceil($total/$limit);
        $countUsers = User::where('email', 'like', '%@localhost.dev')->count();

        if ($countUsers < $needUsers) {
            for ($i = $countUsers; $i < $needUsers; $i++) {
                $this->createUser($i);
            }
        }

        $users = User::where('email', 'like', '%@localhost.dev')->get();

        foreach ($users as $index => $user) {
            $devices = Device::select('devices.id')
                ->skip($limit * $index)
                ->take($limit)
                ->get()
                ->pluck('id')
                ->all();

            $user->devices()->sync($devices);
        }

        $this->line("Job done[OK]\n");
	}

	protected function createUser($index)
    {
        $user = $this->userService->create([
            'email' => "user{$index}@localhost.dev",
        ]);

        $this->userService->setPermissions($user, $this->permissionService->getUserDefaults());
    }
}
