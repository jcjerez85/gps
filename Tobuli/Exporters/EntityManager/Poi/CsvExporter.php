<?php

namespace Tobuli\Exporters\EntityManager\Poi;

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
        if (is_string($item->coordinates)) {
            $item->coordinates = \json_decode($item->coordinates, true);
        }

        $item->coordinates = $item->coordinates['lat'] . ',' . $item->coordinates['lng'];

        if (in_array('icon', $attributes)) {
            $item->icon = $item->mapIcon->url ?? null;
        }

        return parent::parseValues($item, $attributes);
    }
}