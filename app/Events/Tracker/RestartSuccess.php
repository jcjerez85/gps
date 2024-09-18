<?php

namespace App\Events\Tracker;

use App\Events\NoticeEvent;
use Tobuli\Entities\User;

class RestartSuccess extends NoticeEvent
{
    public function __construct(User $actor = null) {
        parent::__construct($actor, NoticeEvent::TYPE_SUCCESS, trans('admin.tracking_service_restarted'));
    }
}
