<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupUnderspeed;
use Tobuli\History\Actions\Speed;
use Tobuli\History\Actions\Underspeed;
use Tobuli\History\Group;
use Tobuli\Reports\DeviceHistoryReport;

class UnderspeedsReport extends DeviceHistoryReport
{
    const TYPE_ID = 6;

    protected $disableFields = ['geofences', 'stops'];
    protected $validation = ['speed_limit' => 'required'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.underspeeds');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,
            Speed::class,
            Underspeed::class,

            GroupUnderspeed::class,
        ];
    }

    protected function getTable($data)
    {
        $rows = [];

        foreach ($data['groups']->all() as $group)
        {
            $rows[] = $this->getDataFromGroup($group, [
                'start_at',
                'end_at',
                'duration',
                'speed_max',
                'speed_avg',
                'location',
            ]);
        }

        return [
            'rows'   => $rows,
            'totals' => [],
        ];
    }

    protected function getTotals(Group $group, array $only = [])
    {
        return parent::getTotals($group, ['underspeed_count']);
    }
}