<?php

namespace Tobuli\Helpers\Templates\Replacers;

use Illuminate\Support\Str;
use Tobuli\Entities\User;

abstract class Replacer
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $prefix;

    abstract public function replacers($model);
    abstract public function placeholders();

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function formatKey($key) {
        if ($this->prefix)
            return "[{$this->prefix}.$key]";

        return "[$key]";
    }

    /**
     * @param $model
     * @param array $fields
     * @return array
     */
    protected function formatFields($model, $fields)
    {
        $replacers = [];

        foreach ($fields as $field)
        {
            $replacers[$this->formatKey($field)] = null;

            if (! empty($model))
                $replacers[$this->formatKey($field)] = $this->getFieldValue($model, $field);
        }

        return $replacers;
    }

    protected function getFieldValue($model, $field) {
        if ($this->user && ! $this->user->can('view', $model, $field))
            return null;

        $method = Str::camel($field) . "Field";

        if ( ! method_exists($this, $method))
            return $model->{$field};

        return call_user_func_array([$this, $method], [$model]);
    }
}