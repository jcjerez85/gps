<?php
/**
 * Created by PhpStorm.
 * User: Linas
 * Date: 2018-03-13
 * Time: 18:36
 */

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;


class ResourseNotFoundException extends HttpException
{
    public function __construct($name)
    {
        parent::__construct(404, trans('global.dont_exist', ['attribute' => trans($name)]));
    }
}