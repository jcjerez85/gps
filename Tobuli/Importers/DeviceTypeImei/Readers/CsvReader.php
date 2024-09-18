<?php

namespace Tobuli\Importers\DeviceTypeImei\Readers;

use Tobuli\Importers\Readers\CsvReader AS BaseCsvReader;

class CsvReader extends BaseCsvReader
{
    public function __construct()
    {
        $this->requiredFieldRules = [
            'imei' => 'required',
        ];
    }
}