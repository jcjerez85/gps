<?php namespace Tobuli\Repositories\Poi;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface PoiRepositoryInterface extends EloquentRepositoryInterface {

    public function whereUserId($user_id);

    public function updateWhereIconIds($ids, $data);

}