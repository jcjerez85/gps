<?php namespace ModalHelpers;

use App\Transformers\Event\EventLookupTransformer;
use CustomFacades\Repositories\EventRepo;
use Illuminate\Support\Arr;
use Tobuli\Entities\Event;
use Formatter;
use FractalTransformer;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;

class EventModalHelper extends ModalHelper {

    public function lookup($data)
    {
        $this->checkException('events', 'view');

        $query = Event::with('device', 'geofence', 'alert')
            ->select('events.*')
            ->orderBy('events.id', 'desc');

        if ( ! empty($data['user_id']))
            $query->where('events.user_id', $data['user_id']);

        if ( ! empty($data['alert_id']))
            $query->where('events.alert_id', $data['alert_id']);

        if ( ! empty($data['device_id']))
            $query->where('events.device_id', $data['device_id']);

        if ( ! empty($data['type']))
            $query->where('events.type', $data['type']);

        if ( ! empty($data['date_from']))
            $query->where('events.time', '>=', Formatter::time()->reverse($data['date_from']));

        if ( ! empty($data['date_to']))
            $query->where('events.time', '<=', Formatter::time()->reverse($data['date_to']));

        if ( ! empty($data['created_from']))
            $query->where('events.created_at', '>=', Formatter::time()->reverse($data['created_from']));

        if ( ! empty($data['created_to']))
            $query->where('events.created_at', '<=', Formatter::time()->reverse($data['created_to']));

        if ( ! empty($data['search'])) {
            $query->leftJoin('devices', 'events.device_id', '=', 'devices.id');

            $query->where(function ($q) use ($data) {
                $q->where('events.message', 'like', '%' . $data['search'] . '%');
                $q->orWhere('devices.name', 'like', '%' . $data['search'] . '%');
            });
        }

        $limit = Arr::get($data, 'limit', 30);
        $limit = min($limit, 1000);

        $events = $query->paginate($limit);

        if ($this->api) {
            $events->getCollection()->transform(function (Event $event)
            {
                return FractalTransformer::setSerializer(WithoutDataArraySerializer::class)
                    ->item($event, EventLookupTransformer::class)->toArray();
            });

            return $events;
        }

        return $events;
    }

    public function search($search, $device_id = null)
    {
        $this->checkException('events', 'view');

        $events = EventRepo::whereUserIdWithAttributes($this->user->id, $search, $device_id);

        foreach ($events as &$event) {
            $event->time = Formatter::time()->convert($event->time);
            $event->speed = Formatter::speed()->convert($event->speed);
            $event->altitude = Formatter::altitude()->convert($event->altitude);
        }

        if ($this->api) {
            $events = $events->toArray();

            foreach ($events['data'] as &$event) {
                if(!empty($event['geofence']))
                    unset($event['geofence']);
            }
            $events['url'] = route('api.get_events');
        }

        return $events;
    }

    public function destroy($id = null)
    {
        $this->checkException('events', 'clean');

        $filter = [
            'id'      => $id,
            'user_id' => $this->user->id,
        ];

        if (empty($filter['id']))
            unset($filter['id']);

        EventRepo::deleteWhere($filter);
    }
}