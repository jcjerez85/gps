<?php

namespace Tobuli\Exporters\EntityManager\Route;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\CsvChunkDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class CsvExporter extends CsvChunkDownloader implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return $this->download($query, $attributes, $filename);
    }

    protected function parseValues($item, array $attributes): array
    {
        if (is_array($item->coordinates)) {
            $item->coordinates = \json_encode($item->coordinates);
        }

        return parent::parseValues($item, $attributes);
    }
}