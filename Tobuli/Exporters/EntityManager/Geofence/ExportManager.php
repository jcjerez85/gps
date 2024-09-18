<?php

namespace Tobuli\Exporters\EntityManager\Geofence;

use Tobuli\Exporters\EntityManager\AbstractFilterExportManager;
use Tobuli\Exporters\EntityManager\Geofence\Filter\GroupsGeofenceFilter;
use Tobuli\Exporters\EntityManager\Geofence\Filter\SinglesGeofenceFilter;
use Tobuli\Exporters\Util\ExportTypesUtil;

class ExportManager extends AbstractFilterExportManager
{
    protected function getBasename(): string
    {
        return 'geofences_report_' . time();
    }

    protected function getExportTypeFiltersMap(): array
    {
        return [
            ExportTypesUtil::EXPORT_TYPE_SINGLE => SinglesGeofenceFilter::class,
            ExportTypesUtil::EXPORT_TYPE_GROUPS => GroupsGeofenceFilter::class,
            ExportTypesUtil::EXPORT_TYPE_ACTIVE => SinglesGeofenceFilter::class,
            ExportTypesUtil::EXPORT_TYPE_INACTIVE => SinglesGeofenceFilter::class,
        ];
    }
}