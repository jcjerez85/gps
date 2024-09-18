<?php

namespace Tobuli\Lookups\Styler;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class ExcelCellStringifier extends StringValueBinder implements FromArray, ShouldAutoSize, WithCustomValueBinder, WithHeadings
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return array_keys($this->data[0] ?? []);
    }

    public function array(): array
    {
        return $this->data;
    }
}