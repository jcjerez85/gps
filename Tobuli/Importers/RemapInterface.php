<?php

namespace Tobuli\Importers;

interface RemapInterface
{
    public function getHeaders($file): array;

    public function remapHeaders(array &$headers);

    public function setFieldsRenameMap(array $fieldsRenameMap);
}