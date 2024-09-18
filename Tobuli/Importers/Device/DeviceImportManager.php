<?php

namespace Tobuli\Importers\Device;

use Tobuli\Importers\Importer;
use Tobuli\Importers\ImportManager;

class DeviceImportManager extends ImportManager
{
    protected function getReadersList(): array
    {
        return [
            'csv' => Readers\DeviceCsvReader::class,
        ];
    }

    public function getImporter(): Importer
    {
        return new DeviceImporter();
    }
}