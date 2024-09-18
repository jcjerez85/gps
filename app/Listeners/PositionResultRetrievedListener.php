<?php

namespace App\Listeners;

use App\Events\NoticeEvent;
use App\Events\PositionResultRetrieved;
use Illuminate\Support\LazyCollection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;

class PositionResultRetrievedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PositionResultRetrieved  $event
     * @return void
     */
    public function handle(PositionResultRetrieved $event)
    {
        $settings = settings('position_notifications');

        if (empty($settings['send_to'])) {
            return;
        }

        $users = $this->getUsers(
            $event->device,
            $settings['send_to'] === 'all',
            $settings['related_user_oldest_record_ago']
        );

        foreach ($users as $user) {
            event(new NoticeEvent($user, NoticeEvent::TYPE_INFO, "{$event->device->name}: {$event->result}"));
        }
    }

    private function getUsers(Device $device, bool $all, int $newerThan): LazyCollection
    {
        if ($all) {
            return $device->users()->select(['id'])->cursor();
        }

        $morphMap = Relation::morphMap();
        $userType = array_search(User::class, $morphMap);

        if ($userType === false) {
            $userType = User::class;
        }

        $imei = $device->imei;

        return User::query()
            ->select('id')
            ->whereIn('id', function (Builder $query) use ($imei, $userType, $newerThan) {
                $query->select('actor_id')
                    ->from('sent_commands')
                    ->where('actor_type', $userType)
                    ->where('device_imei', $imei);

                if ($newerThan) {
                    $query->where('created_at', '>=', \Carbon::now()->subSeconds($newerThan));
                }
            })
            ->cursor();
    }
}
