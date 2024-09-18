<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class DeviceLimitException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, trans('front.devices_limit_reached'));
    }
}