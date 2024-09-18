<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Frontend\SecondaryCredentialsController as BaseSecondaryCredentialsController;

class SecondaryCredentialsController extends BaseSecondaryCredentialsController
{
    public function __construct()
    {
        parent::__construct(request()->user_id);
    }
}
