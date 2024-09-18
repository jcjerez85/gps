<?php

namespace Tobuli\Importers\Readers;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\File;
use Tobuli\Importers\RemapInterface;
use Tobuli\Importers\RemapTrait;

abstract class CsvReader extends Reader implements RemapInterface
{
    use RemapTrait;

    protected $delimiter;

    public function getHeaders($file): array
    {
        $this->readFile($file, $rows, $headers);

        return $headers;
    }

    public function supportsFile(File $file): bool
    {
        return $file->getExtension() == 'csv' && !empty($this->getHeaders($file));
    }

    public function isValidFormat($file): bool
    {
        $this->readFile($file, $rows, $headers);
        $fieldCount = 0;

        if (empty($rows)) {
            return false;
        }

        foreach ($rows as $row) {
            if (! is_array($row)) {
                return false;
            }

            if ($fieldCount == 0) {
                $fieldCount = count($row);

                continue;
            }

            if (count($row) != $fieldCount) {
                return false;
            }
        }

        if (! isset($this->requiredFieldRules)) {
            return true;
        }

        return $this->validateRequiredFields(array_flip($headers));
    }

    public function read($file)
    {
        $this->readFile($file, $rows, $headers);

        return $rows;
    }

    protected function parseRow($row, $headers = [])
    {
        $row = str_replace("{$this->delimiter} ", $this->delimiter, $row);

        $row = str_getcsv($row, $this->delimiter);

        return empty($headers) ? $row : array_combine($headers, $row);
    }

    private function readFile($file, &$rows, &$headerRow)
    {
        $source = file_get_contents($file);

        // remove bom
        $bom = pack('H*','EFBBBF');
        $source = preg_replace("/^$bom/", '', $source);

        $rows = str_getcsv($source, "\n");

        $rows = array_filter($rows, function ($row) {
            return trim($row);
        });

        if (empty($rows)) {
            $rows = null;
            $headerRow = null;

            return;
        }

        $firstRow = array_shift($rows);

        $this->delimiter = $this->detectDelimiter($firstRow);

        $headerRow = $this->parseRow($firstRow);

        if ( ! is_array($headerRow) || empty($headerRow)) {
            $rows = null;
            $headerRow = null;

            return;
        }

        $this->remapHeaders($headerRow);

        foreach ($rows as &$row) {
            try {
                $row = $this->parseRow($row, $headerRow);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    protected function validateRequiredFields($fieldNames)
    {
        $validator = Validator::make($fieldNames, $this->requiredFieldRules);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }

    protected function detectDelimiter($row)
    {
        $delimiters = [
            ";" => 0,
            "," => 0,
            "|" => 0,
            "\t" => 0,
        ];

        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($row, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}
