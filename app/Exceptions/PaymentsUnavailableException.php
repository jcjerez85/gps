<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class PaymentsUnavailableException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, trans('front.payments_service_unavailable'));
    }
}