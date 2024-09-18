<?php

namespace Tobuli\Exporters\EntityManager\Device;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\XlsxChunkDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class XlsExporter implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return (new XlsxChunkDownloader(250))
            ->download($query, $attributes, $filename . 'x');
    }
}