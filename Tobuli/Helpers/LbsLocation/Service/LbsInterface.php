<?php

namespace Tobuli\Helpers\LbsLocation\Service;

interface LbsInterface
{
    public function getLocation(array $data): array;
}