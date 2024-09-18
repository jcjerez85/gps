<?php

namespace Tobuli\Exporters\EntityManager\Device;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\CsvChunkDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class CsvExporter implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return (new CsvChunkDownloader())->setChunkSize(1000)
            ->download($query, $attributes, $filename);
    }
}