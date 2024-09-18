<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class PaymentsConfigurationException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct(404, $message);
    }
}