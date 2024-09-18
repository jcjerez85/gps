<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\User;

class MediaCategoryPolicy extends Policy
{
    protected $permisionKey = 'media_categories';
}
