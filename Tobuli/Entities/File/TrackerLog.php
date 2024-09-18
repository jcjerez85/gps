<?php

namespace Tobuli\Entities\File;

use Illuminate\Support\Str;

class TrackerLog extends FileEntity
{
    protected function getDirectory($entity)
    {
        $config = config('tracker');

        return Str::finish(pathinfo($config['logger.file'], PATHINFO_DIRNAME ), '/');
    }
}
