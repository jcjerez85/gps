<?php

namespace Tobuli\Lookups;

use Yajra\DataTables\Processors\DataProcessor as YajraDataProcessor;

class DataProcessor extends YajraDataProcessor
{
    public function process($object = false)
    {
        $this->output = [];
        $indexColumn  = config('datatables.index_column', 'DT_RowIndex');

        foreach ($this->results as $row) {
            $data  = [];
            $value = $this->addColumns($data, $row);
            $value = $this->editColumns($value, $row);
            $value = $this->setupRowVariables($value, $row);
            $value = $this->selectOnlyNeededColumns($value);
            $value = $this->removeExcessColumns($value);

            if ($this->includeIndex) {
                $value[$indexColumn] = ++$this->start;
            }

            $this->output[] = $object ? $value : $this->flatten($value);
        }

        return $this->escapeColumns($this->output);
    }
}