<?php

namespace Tobuli\Lookups\Tables;

class DevicesGeofenceLookupTable extends DevicesLookupTable
{
    private int $geofenceId;

    public function getTitle()
    {
        return parent::getTitle() . " (" . trans('front.geofence') . ")";
    }

    public function extraQuery($query)
    {
        $this->checkGeofenceId();

        $query->inGeofences($this->geofenceId);

        return parent::extraQuery($query);
    }

    public function setGeofenceId(int $geofenceId): self
    {
        $this->geofenceId = $geofenceId;

        return $this;
    }

    private function checkGeofenceId(): void
    {
        if (isset($this->geofenceId)) {
            return;
        }

        $id = $this->request()->input('id') ?: $this->request()->input('geofence_id');

        if (!$id) {
            return;
        }

        if (!is_numeric($id)) { // ajax wrap protection
            $id = explode('?', $id)[0];
        }

        if (is_numeric($id)) {
            $this->setGeofenceId($id);
        }
    }

    public function getRoute($action, $options = [])
    {
        if (!isset($options['id'])) {
            $this->checkGeofenceId();

            $options['id'] = $this->geofenceId;
        }

        return parent::getRoute($action, $options);
    }
}