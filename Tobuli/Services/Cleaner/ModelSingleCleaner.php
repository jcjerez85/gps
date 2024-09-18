<?php

namespace Tobuli\Services\Cleaner;

use Illuminate\Database\Eloquent\Model;

class ModelSingleCleaner extends ModelCleaner
{
    public function clean()
    {
        return $this->getQuery()->each(function(Model $model) {
            $model->delete();
        });
    }
}