<?php namespace Tobuli\Repositories\Device;

use Illuminate\Support\Arr;
use Tobuli\Entities\Device as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentDeviceRepository extends EloquentRepository implements DeviceRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function find($id) {
        return $this->entity->with('users', 'sensors')->find($id);
    }

    public function whereUserId($user_id) {
        return $this->entity->where(['user_id' => $user_id])->with('traccar', 'icon')->get();
    }

    public function userCount($user_id) {
        return $this->entity->where(['user_id' => $user_id])->count();
    }

    public function updateWhereIconIds($ids, $data)
    {
        $this->entity->whereIn('icon_id', $ids)->update($data);
    }

    public function whereImei($imei) {
        return $this->entity->where('imei', $imei)->first();
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);

        $items = $this->entity
            ->traccarJoin()
            ->select(['devices.*', 'traccar_devices.server_time', 'traccar_devices.time'])
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->with('users')
            ->search(Arr::get($data,'search_phrase'))
            ->where(function ($query) use ($data) {
                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }
            })
            ->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }

    public function getProtocols($ids) {
        return $this->entity
            ->traccarJoin()
            ->distinct('traccar_devices.protocol')
            ->whereIn('devices.id', $ids)
            ->whereNotNull('traccar_devices.protocol')
            ->get();
    }

    public function getByImeiProtocol($imei, $protocol)
    {
        if ($protocol == 'tk103' && strlen($imei) > 11) {
            $device = $this->findWhere(function ($query) use ($imei) {
                $query->where('imei', 'like', '%' . substr($imei, -11));
            });
        } else {
            $device = $this->findWhere(['imei' => $imei]);
        }

        return $device;
    }
}
