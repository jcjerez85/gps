<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class DemoAccountException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, trans('front.demo_acc'));
    }
}