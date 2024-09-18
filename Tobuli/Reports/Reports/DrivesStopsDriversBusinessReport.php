<?php

namespace Tobuli\Reports\Reports;

use Tobuli\History\Actions\AppendDriveBusinessBreak;

class DrivesStopsDriversBusinessReport extends DrivesStopsDriversReport
{
    const TYPE_ID = 21;

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.drives_and_stops').' / '.trans('front.drivers')  . ' ('.trans('front.business').')';
    }

    public static function isEnabled()
    {
        return settings('plugins.business_private_drive.status');
    }

    protected function beforeGenerate() {}

    protected function getActionsList()
    {
        $list = parent::getActionsList();

        $list[] = AppendDriveBusinessBreak::class;

        return $list;
    }
}