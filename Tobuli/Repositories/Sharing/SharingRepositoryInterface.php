<?php namespace Tobuli\Repositories\Sharing;

use Tobuli\Repositories\EloquentRepositoryInterface;

interface SharingRepositoryInterface extends EloquentRepositoryInterface {
    public function getUserSharingByDevice($userId, $deviceId);
}