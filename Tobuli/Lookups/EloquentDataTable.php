<?php

namespace Tobuli\Lookups;

use \Yajra\DataTables\EloquentDataTable as YajraEloquentDataTable;

class EloquentDataTable extends YajraEloquentDataTable
{
    /**
     * @inheritDoc
     */
    public function make($mDataSupport = true)
    {
        try {
            $this->prepareQuery();

            $data = $this->request->has('action')
                ? $this->getAllData($mDataSupport)
                : $this->getPaginatedData($mDataSupport);

            return $this->render($data);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function getAllData($mDataSupport): array
    {
        $data = [];

        $this->query->chunk(2000, function ($items)  use (&$data, $mDataSupport) {
            $processed = $this->processResults($items, $mDataSupport);
            $data = array_merge($data, $this->transform($items, $processed));
        });

        return $data;
    }

    private function getPaginatedData($mDataSupport): array
    {
        $perPage = $this->request->input('length', 10);
        $start   = $this->request->input('start', 0);

        $page = intval($start / $perPage);

        $items = $this->query->paginate($perPage, ['*'], 'page', $page + 1)->items();

        $processed = $this->processResults($items, $mDataSupport);

        return $this->transform($items, $processed);
    }

    public function results()
    {
        return $this->query->get();
    }

    protected function processResults($results, $object = false)
    {
        $processor = new DataProcessor(
            $results,
            $this->getColumnsDefinition(),
            $this->templates,
            $this->request->input('start')
        );

        return $processor->process($object);
    }
}