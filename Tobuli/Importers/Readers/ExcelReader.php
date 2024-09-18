<?php

namespace Tobuli\Importers\Readers;

use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\File;
use Tobuli\Importers\RemapInterface;
use Tobuli\Importers\RemapTrait;

abstract class ExcelReader extends Reader implements RemapInterface
{
    use RemapTrait;

    public function read($file)
    {
        list($headerRow, $rows) = $this->getRows($file);

        $this->remapHeaders($headerRow);

        foreach ($rows as $key => $row) {
            $rows[$key] = array_combine($headerRow, $row);
        }

        if (!is_array($headerRow)) {
            return null;
        }

        if (empty($headerRow)) {
            return null;
        }

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }

    public function supportsFile(File $file): bool
    {
        try {
            IOFactory::load($file);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function isValidFormat($file)
    {
        try {
            list($headerRow, $rows) = $this->getRows($file);

            $this->remapHeaders($headerRow);
        } catch (\PHPExcel_Reader_Exception $e) {
            return false;
        }

        if (empty($rows)) {
            return false;
        }

        if (!array_filter($headerRow)) {
            return false;
        }

        if (!isset($this->requiredFieldRules)) {
            return true;
        }

        return $this->validateRequiredFields(array_flip($headerRow));
    }

    protected function parseRow($row, $headers = [])
    {
        return empty($headers) ? $row : array_combine($headers, $row);
    }

    private function getRows($file): array
    {
        $spreadsheet = IOFactory::load($file);
        $rows = [];

        foreach ($spreadsheet->getAllSheets()[0]->getRowIterator() as $excelRow) {
            $row = [];

            foreach ($excelRow->getCellIterator() as $cell) {
                $row[] = $cell->getValue();
            }

            $rows[] = $row;
        }

        return [array_shift($rows), $rows];
    }

    public function getHeaders($file): array
    {
        $spreadsheet = IOFactory::load($file);
        $rows = [];

        foreach ($spreadsheet->getAllSheets()[0]->getRowIterator() as $excelRow) {
            foreach ($excelRow->getCellIterator() as $cell) {
                $rows[] = $cell->getValue();
            }

            break;
        }

        return $rows;
    }

    protected function validateRequiredFields($fieldNames)
    {
        $validator = Validator::make($fieldNames, $this->requiredFieldRules);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }
}