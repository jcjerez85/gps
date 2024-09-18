<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendDrivePrivateBreak;

class DrivesStopsDriversPrivateReport extends DrivesStopsDriversReport
{
    const TYPE_ID = 22;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drives_and_stops').' / '.trans('front.drivers')  . ' (Private)';
    }

    public static function isEnabled()
    {
        return settings('plugins.business_private_drive.status');
    }

    protected function getActionsList()
    {
        $list = parent::getActionsList();

        $list[] = AppendDrivePrivateBreak::class;

        return $list;
    }
}