<?php

namespace Tobuli\Exporters\EntityManager\Geofence;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\XlsxChunkDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class ExcelExporter extends XlsxChunkDownloader implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return $this->setChunkSize(300)->download($query, $attributes, $filename);
    }

    protected function parseValues($item, array $attributes): array
    {
        if (is_array($item->center)) {
            $item->center = \json_encode($item->center);
        }

        return parent::parseValues($item, $attributes);
    }
}