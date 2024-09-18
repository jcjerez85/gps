<?php

namespace Tobuli\Importers\Task\Readers;

use Carbon\Carbon;
use Tobuli\Importers\Readers\ExcelReader;

class TaskExcelReader extends ExcelReader
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

    public function isValidFormat($file)
    {
        if (!parent::isValidFormat($file)) {
            return false;
        }

        try {
            $headerRow = $this->getHeaders($file);
        } catch (\PHPExcel_Reader_Exception $e) {
            return false;
        }

        if (!$headerRow) {
            return false;
        }

        if (!is_array($headerRow) || !array_filter($headerRow)) {
            return false;
        }

        return true;
    }

    protected function parseRow($row, $headers = [])
    {
        $row = parent::parseRow($row, $headers);

        $datetimeFields = [
            'pickup_time_from',
            'pickup_time_to',
            'delivery_time_from',
            'delivery_time_to'
        ];

        foreach ($datetimeFields as $datetimeField) {
            if (!array_key_exists($datetimeField, $row)) {
                continue;
            }

            try {
                $datetime = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$datetimeField]));
            } catch (\ErrorException $e) {
                $datetime = Carbon::parse($row[$datetimeField]);
            }

            $row[$datetimeField] = $datetime;
        }

        return $row;
    }
}
