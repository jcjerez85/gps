<?php

namespace Tobuli\Exporters\EntityManager\Route;

use Tobuli\Exporters\EntityManager\AbstractFilterExportManager;
use Tobuli\Exporters\EntityManager\Route\Filter\SinglesRouteFilter;
use Tobuli\Exporters\Util\ExportTypesUtil;

class ExportManager extends AbstractFilterExportManager
{
    protected function getBasename(): string
    {
        return 'routes_report_' . time();
    }

    protected function getExportTypeFiltersMap(): array
    {
        return [
            ExportTypesUtil::EXPORT_TYPE_SINGLE => SinglesRouteFilter::class,
            ExportTypesUtil::EXPORT_TYPE_ACTIVE => SinglesRouteFilter::class,
            ExportTypesUtil::EXPORT_TYPE_INACTIVE => SinglesRouteFilter::class,
        ];
    }
}