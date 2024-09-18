<?php namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Tobuli\Entities\BillingPlan;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Tobuli\Services\DeviceService;

class CleanUserCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    private $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        parent::__construct();

        $this->deviceService = $deviceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line("Free users last login more than two months ago.");

        $freePlan = BillingPlan::where('title', 'Free')->first();

        if ( ! $freePlan) {
            $this->line("Can't find free plan");
            die();
        }
        $users = User
            ::where('loged_at', '<', Carbon::now()->subMonths(2))
            ->where('billing_plan_id', $freePlan->id)
            ->get();

        foreach ($users as $user)
        {
            $this->userFullDelete($user);
        }

        $this->line("Free users without object.");

        $users = User
            ::leftJoin('devices', 'users.id', '=', 'devices.user_id')
            ->where('loged_at', '<', Carbon::now()->subMonths(1))
            ->where('billing_plan_id', $freePlan->id)
            ->whereNull('devices.id')
            ->get();

        foreach ($users as $user)
        {
            $this->userFullDelete($user);
        }

        $this->line("Expired users where last login before 3 months and more");

        $users = User
            ::where('subscription_expiration', '!=', '0000-00-00 00:00:00')
            ->where('subscription_expiration', '<', Carbon::now())
            ->where('loged_at', '<', Carbon::now()->subMonths(3))
            ->get();

        foreach ($users as $user)
        {
            $this->userFullDelete($user);
        }

        $this->line("Never connected devices");

        $devices = Device
            ::where('devices.updated_at', '<', Carbon::now()->subMonths(1))
            ->neverConnected()
            ->get();

        foreach ($devices as $device)
        {
            $this->deviceDelete($device);
        }

        $this->line("Job done[OK]\n");
    }

    private function userFullDelete(User $user)
    {
        $this->line($user->id.' '.$user->email);

        $devices = $user->devices;

        foreach ($devices as $device)
        {
            $this->deviceDelete($device);
        }

        DB::table('user_drivers')->where('user_id', $user->id)->delete();

        $user->delete();
    }

    private function deviceDelete(Device $device)
    {
        $this->deviceService->delete($device);
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
