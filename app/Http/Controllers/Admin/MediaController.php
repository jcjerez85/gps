<?php

namespace App\Http\Controllers\Admin;

use Tobuli\Entities\File\DeviceCameraMedia;

class MediaController extends BaseController
{
    public function getSize() {

        $size = DeviceCameraMedia::getDirectorySize();

        return formatBytes( $size );
    }
}
