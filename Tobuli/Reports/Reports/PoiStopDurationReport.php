<?php namespace Tobuli\Reports\Reports;

use Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Poi;
use Tobuli\History\Actions\GroupStop;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\EngineHours;
use Tobuli\Reports\DeviceHistoryReport;

class PoiStopDurationReport extends DeviceHistoryReport
{
    const TYPE_ID = 54;

    protected $disableFields = ['geofences', 'speed_limit', 'stops'];

    protected $stop_duration;

    protected $distance_tolerance;

    public function getInputParameters(string $durationTitle = '', string $durationName = ''): array
    {
        $pois = Poi::where('user_id', $this->user->id);
        $durationTitle = $durationTitle ?: trans('validation.attributes.stop_duration_longer_than') . ' (' . trans('front.minutes') . ')';
        $durationName = $durationName ?: 'stop_duration';

        return [
            \Field::number($durationName, $durationTitle)
                ->setRequired()
            ,
            \Field::number('distance_tolerance',
                trans('validation.attributes.distance_tolerance') . ' (' . trans('front.mt') . ')')
                ->setRequired()
            ,
            Config::get('tobuli.api') == 1
                ? \Field::multiSelect('pois', trans('validation.attributes.pois'))
                ->setOptionsViaQuery($pois, 'name')
                ->setRequired()
                ->addAdditionalParameter('param_omit', true)
                : \Field::multiGroupSelect('pois', trans('validation.attributes.pois'))
                ->setOptionsViaQuery($pois)
                ->setOptionsClosure('groupPois', [$this->user])
                ->setRequired()
                ->addAdditionalParameter('param_omit', true)
            ,
        ];
    }

    public function validateInput(array &$input)
    {
        parent::validateInput($input);

        unset($input['parameters']['pois']);
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.poi_stop_duration');
    }

    protected function beforeGenerate()
    {
        parent::beforeGenerate();

        $this->stop_duration = Arr::get($this->parameters, 'stop_duration', 0) * 60;
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

    protected function generateDevice($device)
    {
        if ($error = $this->precheckError($device))
            return [
                'meta' => $this->getDeviceMeta($device),
                'error' => $error
            ];

        $data = $this->getDeviceHistoryData($device);

        if ($this->isEmptyResult($data))
            return null;

        $rows = $this->getRows($data);

        if (empty($rows))
            return null;

        return [
            'meta' => $this->getDeviceMeta($device) + $this->getHistoryMeta($data['root']),
            'table'  => [
                'rows'   => $rows,
                'totals' => [],
            ],
            'totals' => $this->getTotals($data['root'])
        ];
    }

    protected function getRows($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $duration = $group->stats()->get('duration')->value();

            if ($duration < $this->stop_duration)
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

    /**
     * @param $position
     * @return Poi|null
     */
    protected function getPoiIn($position)
    {
        foreach ($this->pois as $poi) {
            if ( ! $poi->pointIn($position, $this->distance_tolerance))
                continue;

            return $poi;
        }

        return null;
    }
}