<?php

namespace Tobuli\Importers\Task\Readers;

use Tobuli\Importers\Readers\CsvReader;

class TaskCsvReader extends CsvReader
{
    public function __construct()
    {
        $this->requiredFieldRules = [
            'title' => 'required',
            'device_id' => 'required_without:imei',
            'imei' => 'required_without:device_id',
            'priority' => 'required',
            'pickup_address_lat' => 'required',
            'pickup_address_lng' => 'required',
            'pickup_time_from' => 'required',
            'pickup_time_to' => 'required',
            'delivery_address_lat' => 'required',
            'delivery_address_lng' => 'required',
            'delivery_time_from' => 'required',
            'delivery_time_to' => 'required',
        ];
    }
}
