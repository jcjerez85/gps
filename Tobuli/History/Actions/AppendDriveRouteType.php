<?php

namespace Tobuli\History\Actions;


use Illuminate\Database\Eloquent\Collection;
use Tobuli\Entities\DeviceRouteType;

class AppendDriveRouteType extends ActionAppend
{
    /**
     * @var Collection
     */
    protected $routes = [];

    /**
     * @var DeviceRouteType
     */
    protected $current;

    public function boot(){
        if ( ! settings('plugins.business_private_drive.status') )
            return;

        $this->getRouteTypes();
        $this->setCurrent();
    }

    public function proccess(&$position)
    {
        $position->drive_route_type = null;

        if (empty($this->current)) {
            return;
        }

        if (strtotime($this->current->started_at) > $position->timestamp) {
            return;
        }

        if (strtotime($this->current->ended_at) < $position->timestamp) {
            $this->setCurrent();
            $this->proccess($position);
            return;
        }

        $position->drive_route_type = $this->current->type;
    }

    protected function setCurrent()
    {
        $this->current = $this->routes->shift();
    }

    protected function getRouteTypes()
    {
        $date_from = $this->history->getDateFrom();
        $date_to = $this->history->getDateTo();
        $device = $this->history->getDevice();

        $this->routes = DeviceRouteType::query()
            ->where('device_id', $device->id)
            ->where(function($query) use ($date_from, $date_to){
                $query
                    ->whereBetween('started_at', [$date_from, $date_to])
                    ->orWhereBetween('ended_at', [$date_from, $date_to]);
            })
            ->orderBy('started_at', 'asc')
            ->orderBy('ended_at', 'asc')
            ->get();
    }
}