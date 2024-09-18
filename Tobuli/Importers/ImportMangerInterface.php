<?php

namespace Tobuli\Importers;

use Symfony\Component\HttpFoundation\File\File;

interface ImportMangerInterface
{
    public function import($file, array $additionals = []);

    public function getImportFields(File $file): array;
}