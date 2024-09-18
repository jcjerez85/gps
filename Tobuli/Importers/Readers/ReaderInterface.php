<?php

namespace Tobuli\Importers\Readers;

use Symfony\Component\HttpFoundation\File\File;

interface ReaderInterface
{
    public function isValidFormat($file);

    public function read($file);

    public function supportsFile(File $file): bool;
}