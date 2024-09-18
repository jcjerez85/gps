<?php

namespace Tobuli\Exporters\EntityManager;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse;
}