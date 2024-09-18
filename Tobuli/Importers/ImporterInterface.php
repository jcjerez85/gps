<?php

namespace Tobuli\Importers;

interface ImporterInterface
{
    public function import($file, $additionals);
}