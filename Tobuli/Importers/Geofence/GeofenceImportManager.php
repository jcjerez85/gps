<?php

namespace Tobuli\Importers\Geofence;

use Tobuli\Importers\Importer;
use Tobuli\Importers\ImportManager;

class GeofenceImportManager extends ImportManager
{
    protected function getReadersList(): array
    {
        return [
            'kml'     => Readers\GeofenceKmlReader::class,
            'geojson' => Readers\GeofenceGeoJSONReader::class,
            'json'    => Readers\GeofenceGeoJSONReader::class,
            'gexp'    => Readers\GeofenceGexpReader::class,
        ];
    }

    public function getImporter(): Importer
    {
        return new GeofenceImporter();
    }
}