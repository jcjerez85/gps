<?php namespace Tobuli\Repositories\Sharing;

use Illuminate\Support\Facades\Auth;
use Tobuli\Repositories\EloquentRepository;
use Tobuli\Entities\Sharing as Entity;

class EloquentSharingRepository extends EloquentRepository implements SharingRepositoryInterface {

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getUserSharing($userId)
    {
        $result = $this->entity
            ->where('user_id', $userId)
            ->get();

        return $result;
    }

    public function getUserSharingByDevice($userId, $deviceId)
    {
        $result = $this->entity
            ->where('user_id', $userId)
            ->whereHas('devices', function($query) use($deviceId) {
                $query->where('device_id', $deviceId);
            })
            ->get();

        return $result;
    }

    public function getSharingWith($sharingId, $with)
    {
        return $this->entity->with($with)->find($sharingId);
    }
}
