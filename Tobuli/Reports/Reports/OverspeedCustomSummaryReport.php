<?php namespace Tobuli\Reports\Reports;


class OverspeedCustomSummaryReport extends OverspeedCustomReport
{
    const TYPE_ID = 34;

    protected $disableFields = ['geofences', 'stops', 'show_addresses', 'zones_instead'];

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.overspeed_custom_summary');
    }
}