<?php

namespace Tobuli\Importers\Route;

use Tobuli\Importers\Importer;
use Tobuli\Importers\ImportManager;

class RouteImportManager extends ImportManager
{
    protected function getReadersList(): array
    {
        return [
            'kml' => Readers\RouteKmlReader::class,
        ];
    }

    public function getImporter(): Importer
    {
        return new RouteImporter();
    }
}