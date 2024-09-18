<?php

namespace Tobuli\Lookups\Tables;

use Tobuli\Lookups\LookupTable;
use Tobuli\Lookups\Models\LookupDevice;
use Tobuli\Lookups\Models\LookupEvent;

class EventsLookupTable extends LookupTable
{
    protected function getLookupClass()
    {
        return LookupEvent::class;
    }

    /*
     * @return string
     */
    public function getTitle()
    {
        return trans('front.events');
    }

    /*
     * @return string
     */
    public function getIcon()
    {
        return 'icon events';
    }

    /*
     * @return array
     */
    public function getDefaultColumns() {
        return [
            'time',
            'device',
            'type',
            'message',
        ];
    }

    public function getDefaultOrder()
    {
        return ['time', 'desc'];
    }

    public function baseQuery()
    {
        $query = $this->getUser()->events();

        //remove default order in relationship
        //$query->getQuery()->clearOrdersBy();

        return $query;
    }

    public function getRowActions($event)
    {
        $user = $this->getUser();

        if ( ! $user)
            return [];

        $actions = [];

        if ($user->can('remove', $event))
            $actions[] = [
                'title' => trans('global.delete'),
                'url'   => route("events.do_destroy", ['id' => $event->id]),
                'modal' => 'events_do_destroy',
            ];

        if ($user->perm('call_action', 'edit'))
            $actions[] = [
                'title' => trans('front.call_action'),
                'url'   => route("call_actions.create_by_event", ['event_id' => $event->id]),
                'modal' => 'call_action_create',
            ];

        return $actions;
    }
}