<?php

namespace Tobuli\Exporters\EntityManager;

use Tobuli\Exporters\Util\FilterInterface;

abstract class AbstractFilterExportManager extends AbstractExportManager
{
    public function applyFilter(string $exportType, array $request): self
    {
        $dataLoader = $this->findFilter($exportType);
        $dataLoader->applyFilter($this->query, $request);

        return $this;
    }

    protected function findFilter(string $exportType): FilterInterface
    {
        $filtersMap = $this->getExportTypeFiltersMap();

        if (!isset($filtersMap[$exportType])) {
            throw new \LogicException('Export type not found');
        }

        $class = $filtersMap[$exportType];

        return new $class();
    }

    abstract protected function getExportTypeFiltersMap(): array;
}