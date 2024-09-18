<?php

namespace Tobuli\Exporters\EntityManager\Route;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\JsonDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class GexpExporter implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        $data = $query->get($attributes)->toArray();

        return (new JsonDownloader())
            ->download($data, $filename);
    }
}