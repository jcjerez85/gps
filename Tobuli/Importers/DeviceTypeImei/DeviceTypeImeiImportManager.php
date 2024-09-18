<?php

namespace Tobuli\Importers\DeviceTypeImei;

use Tobuli\Importers\Importer;
use Tobuli\Importers\ImportManager;

class DeviceTypeImeiImportManager extends ImportManager
{
    protected function getReadersList(): array
    {
        return [
            'csv' => Readers\CsvReader::class,
        ];
    }

    public function getImporter(): Importer
    {
        return new DeviceTypeImeiImporter();
    }
}