<?php

namespace Tobuli\Services\Cleaner;

interface DateCleanerInterface
{
    public function clean();

    public function setDate($date);

    public function setDateField(string $dateField);
}