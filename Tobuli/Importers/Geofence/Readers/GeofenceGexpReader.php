<?php

namespace Tobuli\Importers\Geofence\Readers;

use Tobuli\Importers\Readers\GexpReader;

class GeofenceGexpReader extends GexpReader
{
    protected function parsePoint($data)
    {
        if ( ! empty($data['coordinates'])) {
            if (is_string($data['coordinates']))
                $data['coordinates'] = json_decode($data['coordinates'], true);

            $data['polygon'] = $data['coordinates'];
            unset($data['coordinates']);
        }

        if ( ! empty($data['group_id'])) {
            $data['group'] = $this->groups[$data['group_id']] ?? 0;
        }

        return $data;
    }
}
