<?php namespace Tobuli\Repositories\TrackerPort;

use Tobuli\Entities\TrackerPort as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentTrackerPortRepository extends EloquentRepository implements TrackerPortRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function getProtocolList()
    {
        $result = [];

        $protocols = $this->entity->all();

        foreach ($protocols as $protocol) {
            $result[$protocol->name] = $protocol->display;
        }

        $result = array_merge(config('tobuli.additional_protocols'), $result);

        asort($result);

        return $result;
    }
}