<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class PermissionException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, trans('front.dont_have_permission'));
    }
}