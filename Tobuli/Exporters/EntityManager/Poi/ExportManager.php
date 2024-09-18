<?php

namespace Tobuli\Exporters\EntityManager\Poi;

use Tobuli\Exporters\EntityManager\AbstractFilterExportManager;
use Tobuli\Exporters\EntityManager\Poi\Filter\GroupsPoiFilter;
use Tobuli\Exporters\EntityManager\Poi\Filter\SinglesPoiFilter;
use Tobuli\Exporters\Util\ExportTypesUtil;

class ExportManager extends AbstractFilterExportManager
{
    protected function getBasename(): string
    {
        return 'poi_report_' . time();
    }

    protected function getExportTypeFiltersMap(): array
    {
        return [
            ExportTypesUtil::EXPORT_TYPE_SINGLE => SinglesPoiFilter::class,
            ExportTypesUtil::EXPORT_TYPE_GROUPS => GroupsPoiFilter::class,
            ExportTypesUtil::EXPORT_TYPE_ACTIVE => SinglesPoiFilter::class,
            ExportTypesUtil::EXPORT_TYPE_INACTIVE => SinglesPoiFilter::class,
        ];
    }
}