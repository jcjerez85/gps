<?php

namespace Tobuli\Helpers\LbsLocation\Service;

abstract class AbstractCustomLbs extends AbstractLbs
{
    protected function append(array $src, string $srcKey, array &$dst, string $dstKey)
    {
        if (isset($src[$srcKey])) {
            $dst[$dstKey] = $src[$srcKey];
        }
    }
}