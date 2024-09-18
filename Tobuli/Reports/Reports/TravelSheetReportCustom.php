<?php namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\GroupDriveStop;
use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Drivers;
use Tobuli\History\Actions\DriveStop;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\Fuel;
use Tobuli\History\Actions\Speed;

class TravelSheetReportCustom extends TravelSheetReport
{
    const TYPE_ID = 39;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.travel_sheet_custom');
    }

    protected function getActionsList()
    {
        return [
            DriveStop::class,
            Duration::class,
            Distance::class,
            Speed::class,
            Fuel::class,
            Drivers::class,

            GroupDriveStop::class,
        ];
    }

    protected function getTable($data)
    {
        $table = parent::getTable($data);

        if ( ! empty($table['rows']) && $table['rows'][0]['group_key'] != 'drive')
            array_shift($table['rows']);

        return $table;
    }
}