<?php

namespace App\Transformers\ApiV1;

use App\Transformers\BaseTransformer;
use Formatter;
use Tobuli\Entities\Device;

class DeviceFullJsonTransformer extends DeviceFullTransformer {

    protected $json = true;
}