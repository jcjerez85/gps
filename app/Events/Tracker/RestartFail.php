<?php

namespace App\Events\Tracker;

use App\Events\NoticeEvent;
use Tobuli\Entities\User;

class RestartFail extends NoticeEvent
{
    public function __construct(User $actor = null) {
        parent::__construct($actor, NoticeEvent::TYPE_ERROR, trans('admin.unable_to_start_tracker_server'));
    }
}
