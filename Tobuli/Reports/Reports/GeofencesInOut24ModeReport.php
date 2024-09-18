<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\Distance;
use Tobuli\History\Actions\Duration;
use Tobuli\History\Actions\GroupDailySplit;
use Tobuli\History\Actions\GroupGeofenceIn;

class GeofencesInOut24ModeReport extends GeofencesInOutReport
{
    const TYPE_ID = 15;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.geofence_in_out_24_mode');
    }

    protected function getActionsList()
    {
        return [
            Duration::class,
            Distance::class,

            GroupDailySplit::class,
            GroupGeofenceIn::class,
        ];
    }
}