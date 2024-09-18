<?php

namespace Tobuli\Services\Cleaner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModelCleaner extends AbstractCleaner
{
    /**
     * @var Model
     */
    private $model;

    public function setModel(string $model): self
    {
        if (strpos($model, '\\Tobuli\\Entities\\') !== 0) {
            $model = '\\Tobuli\\Entities\\' . ucfirst(Str::camel($model));
        }

        $this->model = new $model();

        return $this;
    }

    public function clean()
    {
        return $this->getQuery()->delete();
    }

    protected function getQuery(): Builder
    {
        $query = $this->model->newQuery()
            ->where($this->dateField, '<', $this->date);

        if ($this->limit) {
            $query->limit($this->limit);
        }

        return $query;
    }
}