<?php

namespace Tobuli\Reports\Reports;

use Tobuli\Exceptions\ValidationException;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\GroupGeofenceGroupShifts;
use Tobuli\History\Actions\GroupGeofenceShifts;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

abstract class AbstractGeofencesStopReport extends DeviceHistoryReport
{
    protected $disableFields = ['speed_limit', 'show_addresses', 'zones_instead'];
    protected $validation = ['geofences' => 'required'];

    private bool $mandatoryStop;

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            $this->parameters['group_geofences'] ? GroupGeofenceGroupShifts::class : GroupGeofenceShifts::class,
        ];
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();

        $this->mandatoryStop = !isset($this->parameters['mandatory_stop']) || $this->parameters['mandatory_stop'];
    }

    public function getInputParameters(): array
    {
        $inputParameters = [];

        $inputParameters[] = \Field::select('group_geofences', trans('front.group_geofences'), 0)
            ->setOptions([0 => trans('global.no'), 1 => trans('global.yes')])
            ->setRequired();

        $inputParameters[] = \Field::select('mandatory_stop', trans('front.mandatory_stop'), 1)
            ->setOptions([0 => trans('global.no'), 1 => trans('global.yes')]);

        return $inputParameters;
    }

    protected function isGroupCalculated(Group $group): bool
    {
        return !$this->mandatoryStop || $group->stats()->get('stop_count')->value();
    }

}