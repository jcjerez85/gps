<?php

namespace Tobuli\Importers;

trait RemapTrait
{
    protected $fieldsRenameMap = [];
    
    public function remapHeaders(array &$headers)
    {
        foreach ($headers as &$header) {
            $originalField = array_search($header, $this->fieldsRenameMap);

            if ($originalField !== false && $originalField !== $header) {
                $header = $originalField;
            }
        }
    }

    public function setFieldsRenameMap(array $fieldsRenameMap)
    {
        $this->fieldsRenameMap = array_filter($fieldsRenameMap);
    }
}