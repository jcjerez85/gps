<?php

namespace Tobuli\Exporters\EntityManager\Device;

use Tobuli\Exporters\EntityManager\AbstractExportManager;

class ExportManager extends AbstractExportManager
{
    protected function getBasename(): string
    {
        return 'devices_report_' . time();
    }
}