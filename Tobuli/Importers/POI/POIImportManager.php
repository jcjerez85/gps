<?php

namespace Tobuli\Importers\POI;

use Tobuli\Importers\Importer;
use Tobuli\Importers\ImportManager;

class POIImportManager extends ImportManager
{
    protected function getReadersList(): array
    {
        return [
            'kml'     => Readers\POIKmlReader::class,
            'gpx'     => Readers\POIGpxReader::class,
            'geojson' => Readers\POIGeoJSONReader::class,
            'json'    => Readers\POIGeoJSONReader::class,
            'csv'     => Readers\POICsvReader::class,
        ];
    }

    public function getImporter(): Importer
    {
        return new POIImporter();
    }
}