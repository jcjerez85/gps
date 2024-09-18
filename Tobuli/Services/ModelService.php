<?php

namespace Tobuli\Services;

use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;


abstract class ModelService
{
    protected $defaults = [];

    protected $rulesStore = [];
    protected $rulesUpdate = [];

    protected abstract function store(array $data);
    protected abstract function update($model, array $data);
    protected abstract function delete($model);

    protected function normalize(array $data)
    {
        return $data;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function create(array $data)
    {
        $data = $this->mergeDefaults($data);
        $data = $this->normalize($data);

        $this->validateStore($data);

        return $this->store($data);
    }

    public function edit($model, array $data)
    {
        $data = $this->normalize($data);

        $this->validateUpdate($data);

        return $this->update($model, $data);
    }

    public function remove($model)
    {
        return $this->delete($model);
    }

    protected function mergeDefaults($data)
    {
        $defaults = $this->getDefaults();

        foreach ($defaults as $key => $value) {
            if (isset($data[$key]) && empty($data[$key])) {
                unset($data[$key]);
            }
        }

        return empty($data) ? $defaults : array_merge($defaults, $data);
    }

    public function getValidationRulesStore()
    {
        return $this->rulesStore;
    }

    public function setValidationRulesStore(array $rules)
    {
        $this->rulesStore = $rules;
    }

    public function getValidationRulesUpdate()
    {
        return $this->rulesUpdate;
    }

    public function setValidationRulesUpdate(array $rules)
    {
        $this->rulesUpdate = $rules;
    }

    public function validateStore($data)
    {
        $this->validate($data, $this->getValidationRulesStore());
    }

    public function validateUpdate($data)
    {
        $this->validate($data, $this->getValidationRulesUpdate());
    }

    protected function validate($data, $rules)
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails())
            throw new ValidationException($validator->messages());
    }
}
