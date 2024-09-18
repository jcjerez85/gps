<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\GroupShift;
use Tobuli\History\Actions\Speed;
use Tobuli\Reports\DeviceHistoryReport;

class GeneralInformationShiftReport extends DeviceHistoryReport
{
    const TYPE_ID = 81;

    protected $disableFields = ['geofences', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return 81;
    }

    public function title()
    {
        return trans('front.general_information') . ' (' . trans('front.shift_time') . ')';
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            EngineHours::class,
            GroupShift::class,
        ];
    }

    public function getInputParameters(): array
    {
        $timeSelectOptions = ['' => trans('admin.select')] + getSelectTimeRange();
        $inputParameters = [
            \Field::select('shift_start', trans('validation.attributes.shift_start'))
                ->setOptions($timeSelectOptions)
                ->setValidation('required|date_format:H:i'),
            \Field::select('shift_finish', trans('validation.attributes.shift_finish'))
                ->setOptions($timeSelectOptions)
                ->setValidation('required|date_format:H:i'),
        ];

        return array_merge($inputParameters, parent::getInputParameters());
    }

    protected function isEmptyResult($data)
    {
        return empty($data['groups']) || empty($data['groups']->all());
    }

    protected function generateDevice($device)
    {
        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $total = $data['groups']->merge();

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'totals' => $this->getDataFromGroup($total, [
                'start_at',
                'end_at',
                'distance',
                'drive_duration',
                'stop_duration',
                'engine_hours',
                'speed_max',
                'fuel_consumption',
            ])
        ];
    }

    public function getShiftTime(): string
    {
        return $this->parameters['shift_start'] . ' - ' . $this->parameters['shift_finish'];
    }
}