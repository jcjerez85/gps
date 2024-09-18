<?php namespace Tobuli\Repositories\Poi;

use Tobuli\Entities\Poi as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentPoiRepository extends EloquentRepository implements PoiRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereUserId($user_id) {
        return Entity::where('user_id', $user_id)->with('mapIcon')->get();
    }

    public function updateWhereIconIds($ids, $data)
    {
        $this->entity->whereIn('map_icon_id', $ids)->update($data);
    }

}