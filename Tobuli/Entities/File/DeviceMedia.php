<?php

namespace Tobuli\Entities\File;

class DeviceMedia extends FileEntity
{
    protected function getDirectory($device)
    {
        return cameras_media_path($device->imei);
    }
}
