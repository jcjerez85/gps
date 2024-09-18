<?php namespace Tobuli\Repositories\Event;

use Tobuli\Entities\Event as Entity;
use Tobuli\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Formatter;

class EloquentEventRepository extends EloquentRepository implements EventRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id) {
        return Entity::where('user_id', $user_id)->get();
    }

    public function whereUserIdWithAttributes($user_id, $search, $device_id = null) {
        $queryy = Entity::with('device', 'geofence')
            ->select('events.*')
            ->addSelect('devices.name AS device_name')
            ->orderBy('id', 'desc')
            ->leftJoin('devices', 'events.device_id', '=', 'devices.id')
            ->where('events.user_id', $user_id);

        if (!empty($device_id)) {
            $queryy->where('devices.id', $device_id);
        }

        if (empty($search)) {
            $events = $queryy->paginate(30);
        }
        else {
            $events = $queryy->where(function ($query) use ($search) {
                $query->where('events.message', 'like', '%' . $search . '%');
                $query->orWhere('devices.name', 'like', '%' . $search . '%');
            })
            ->limit(50)->get();
        }

        return $events;
    }

    public function findWithAttributes($id) {
        return Entity::where('id', $id)->with('device', 'alert')->first();
    }

    public function getHigherTime($user_id, $time, $device_id = null) {
        return Entity::where(function($query) use($user_id, $time, $device_id) {
            if ($device_id) {
                if (is_array($device_id))
                    $query->whereIn('device_id', $device_id);
                else
                    $query->where('device_id', $device_id);
            }
            $query->where('user_id', $user_id);
            $query->where('created_at', '>', date('Y-m-d H:i:s', $time));
            $query->whereNull('silent');
            return $query;
        })->with('geofence', 'device', 'alert')->get();
    }

    public function search($data) {
        $date_from = Formatter::time()->reverse($data['from_date'].' '.$data['from_time']);
        $date_to = Formatter::time()->reverse($data['to_date'].' '.$data['to_time']);

        return Entity::orderBy('time', 'asc')
            ->with(['geofence'])
            ->whereBetween('time', [$date_from, $date_to])
            ->where('device_id', $data['device_id'])
            ->where('user_id', $data['user_id'])
            ->get()
            ->toArray();
    }

    public function getBetween($user_id, $device_id, $from, $to) {
        return $this->entity
            ->with(['geofence'])
            ->whereBetween('time', [$from, $to])
            ->where([
                'user_id' => $user_id,
                'device_id' => $device_id
            ])
            ->orderBy('time', 'asc')
            ->get();
    }

    public function getBetweenCount($user_id, $device_id, $from, $to) {
        return $this->entity
            ->select('*', DB::raw('count(*) as total'))
            ->with(['geofence'])
            ->whereBetween('time', [$from, $to])
            ->where([
                'user_id' => $user_id,
                'device_id' => $device_id
            ])
            ->groupBy(['alert_id', 'type'])
            ->get();
    }
}