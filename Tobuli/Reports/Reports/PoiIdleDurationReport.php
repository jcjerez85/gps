<?php namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Poi;
use Tobuli\History\Actions\GroupStop;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\History\Actions\GeofencesIn;
use Tobuli\Reports\DeviceHistoryReport;

class PoiIdleDurationReport extends PoiStopDurationReport
{
    const TYPE_ID = 55;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    protected $idle_duration;

    protected $distance_tolerance;

    public function getInputParameters(string $durationTitle = '', string $durationName = ''): array
    {
        $durationTitle = trans('validation.attributes.idle_duration_longer_than') . ' (' . trans('front.minutes') . ')';
        $durationName = 'idle_duration';

        return parent::getInputParameters($durationTitle, $durationName);
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.poi_idle_duration');
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();
        
        $this->idle_duration = Arr::get($this->parameters, 'idle_duration', 0) * 60;
        $this->distance_tolerance = Arr::get($this->parameters, 'distance_tolerance', 0) / 1000;
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            EngineHours::class,

            GroupStop::class,
        ];
    }

    protected function getRows($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $duration = $group->stats()->get('engine_idle')->value();

            if ($duration < $this->idle_duration)
                continue;

            $poi = $this->getPoiIn($group->getStartPosition());

            if ( ! $poi)
                continue;

            $distance = $poi->pointDistance($group->getStartPosition());

            $rows[] = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'duration',
                'engine_idle',
                'location',
            ]) + [
                'near' => Formatter::distance()->human($distance) . ' - ' . $poi->name
            ];
        }

        return $rows;
    }
}