<?php

namespace Tobuli\Lookups;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tobuli\Entities\User;

abstract class LookupModel
{
    protected $defaults;

    protected $columns;

    protected $model;

    protected $user;

    abstract protected function modelClass();
    abstract protected function listColumns();

    public function __construct(User $user)
    {
        $this->defaults = [
            'name'           => null,
            'title'          => null,
            'render'         => null,
            'orderable'      => true,
            'searchable'     => true,
            'exportable'     => true,
            'printable'      => true,
        ];

        $this->user = $user;

        $this->columns = new Collection();

        $this->listColumns();
    }

    public function hasColumn($data) {
        return $this->columns->has($data);
    }

    public function getColumn($data) {
        return $this->columns->get($data);
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getColumnsOnly(array $columns) {
        return $this->getColumns()->filter(function($field) use ($columns){
            return in_array($field['data'], $columns);
        })->sortBy(function($field) use ($columns){
            return array_search($field['data'], $columns);
        });
    }

    public function setColumns(array $column) {
        return $this->columns->put($column['data'], array_merge($this->defaults, $column));
    }

    public function addColumn($options) {
        if (is_string($options))
            $options = [
                'data'           => $options,
                'name'           => $options,
                'title'          => trans("validation.attributes.{$options}"),
            ];

        $this->setColumns($options);
    }

    public function renderHtml($model, $data) {
        $column = $this->getColumn($data);

        $renderMethod = Str::camel("renderHtml" . $column['data']);

        if (method_exists($this, $renderMethod))
            return call_user_func_array([$this, $renderMethod], [$model]);

        return $this->render($model, $data);
    }

    public function render($model, $data)
    {
        list($relation, $property, $renderMethod) = Cache::store('array')
            ->rememberForever("lookup_column_$data", function() use ($data) {
                $column = $this->getColumn($data);

                try {
                    list($relation, $property) = explode('.', $column['name']);
                } catch (\Exception $e) {
                    $relation = null;
                    $property = $column['name'];
                }

                $renderMethod = Str::camel("render" . $column['data']);

                if (!method_exists($this, $renderMethod))
                    $renderMethod = null;

                return [$relation, $property, $renderMethod];
            });

        if ($renderMethod)
            return call_user_func_array([$this, $renderMethod], [$model]);

        if ( ! $relation) {
            return $model->{$property};
        }

        $data = $model->{$relation};

        if ( ! $data)
            return null;

        if ($data instanceof Collection) {
            return $data->implode($property, ', ');
        }

        return $data->{$property};
    }

    public function order($query, $data, $desc) {
        $column = $this->getColumn($data);

        if ( ! $column['orderable'])
            return $query;

        $orderMethod = Str::camel("order" . $column['data']);

        if (method_exists($this, $orderMethod))
            return call_user_func_array([$this, $orderMethod], [$query, $desc]);

        return $query->orderBy($column['name'], $desc);
    }

    public function search($query, $data, $value) {
        $column = $this->getColumn($data);

        if ( ! $column['searchable'])
            return $query;

        $searchMethod = Str::camel("search" . $column['data']);

        if (method_exists($this, $searchMethod))
            return call_user_func_array([$this, $searchMethod], [$query, $value]);

        try {
            list($relation, $property) = explode('.', $column['name']);
        } catch (\Exception $e) {
            $relation = null;
            $property = $column['name'];
        }

        if ($relation)
            return $query->orWhere($column['name'], 'like', "%$value%");

        return $query->orWhere("devices.$property", 'like', "%$value%");
    }

    public function filter($query, $data, $value) {
        $column = $this->getColumn($data);

        $filterMethod = Str::camel("filter" . $column['data']);

        if (method_exists($this, $filterMethod))
            return call_user_func_array([$this, $filterMethod], [$query, $value]);

        return $query->where($column['name'], $value);
    }

    public function model()
    {
        if ( ! is_null($this->model))
            return $this->model;

        $class = $this->modelClass();

        return $this->model = new $class();
    }

    protected function getQueryColumn($column) {

    }
}