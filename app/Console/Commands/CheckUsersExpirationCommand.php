<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tobuli\Entities\Event;
use Tobuli\Entities\SendQueue;
use Tobuli\Entities\User;

class CheckUsersExpirationCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'users_expiration:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates users expiration events.';

    /**
     * Create a new command instance.
     *
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
        if (settings('main_settings.expire_notification.active_before'))
        {
            $days_before = settings('main_settings.expire_notification.days_before');

            $expiring = User::with('eventsLog')
                ->isExpiringAfter($days_before)
                ->whereDoesntHave('eventsLog', function ($query) use ($days_before) {
                    $query
                        ->where('type', Event::TYPE_EXPIRING_USER)
                        ->whereRaw('`events_log`.`time` <= `users`.`subscription_expiration`')
                        ->whereRaw('`events_log`.`time` >= DATE_SUB(`users`.`subscription_expiration`, INTERVAL '. $days_before .' DAY)');
                })->get();

            $this->createEvents(Event::TYPE_EXPIRING_USER, $expiring);
        }

        if (settings('main_settings.expire_notification.active_after'))
        {
            $days_after = settings('main_settings.expire_notification.days_after');

            $expired = User::with('eventsLog')
                ->isExpiredBefore($days_after)
                ->whereDoesntHave('eventsLog', function ($query) {
                    $query
                        ->where('type', Event::TYPE_EXPIRED_USER)
                        ->whereRaw('`events_log`.`time` >= `users`.`subscription_expiration`');
                })->get();

            $this->createEvents(Event::TYPE_EXPIRED_USER, $expired);
        }

        echo "DONE\n";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    private function createEvents($type, $users)
    {
        foreach ($users as $user)
        {
            SendQueue::create([
                'user_id'   => $user->id,
                'type'      => $type,
                'data'      => $user,
                'channels'  => [
                    'push'  => true,
                    'email' => empty($user->manager->email) ? [$user->email] : [$user->email, $user->manager->email],
                    'mobile_number' => $user->mobile_number,
                ]
            ]);

            $user->logEvent($type);
        }
    }
}