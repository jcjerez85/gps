<?php
namespace Tobuli\Services;

use Tobuli\Entities\CustomField;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Validator;

class CustomValuesService
{
    /**
     * Save custom values for an item
     *
     * @param Device|User $item Device or User model
     * @param array $customValues
     * @return void
     */
    public function saveCustomValues($item, $customValues)
    {
        if (! isset($item)) {
            return;
        }

        if (! isset($customValues)) {
            return;
        }

        $this->validate($item, $customValues);

        $item->setCustomValues($customValues);
        $item->saveCustomValues();
    }

    /**
     * Validate custom values for an item
     *
     * @param Device|User $item Device or User model
     * @param array $data
     * @return void
     */
    public function validate($item, $data)
    {
        $validator = $this->getValidator($item, $data);

        if ($validator->fails()) {
            throw new ValidationException($validator->messages());
        }
    }

    /**
     * Get validator for specific model and data
     *
     * @param Device|User $item
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator($item, $data)
    {
        return Validator::make($data, $this->getValidationRules($item, $data));
    }

    /**
     * Get validation rules for specific model and data
     *
     * @param Device|User $item
     * @param array $data
     * @return array
     */
    private function getValidationRules($item, $data)
    {
        $rules = [];
        $customFields = $item->customFields()->get();

        foreach ($customFields as $field) {
            $rules[$field->slug] = $this->getFieldValidationRules($field);

            if ($field->data_type != 'multiselect') {
                continue;
            }

            $rules = array_replace(
                $rules,
                $this->getArrayRules(
                    $data[$field->slug] ?? [],
                    'in:'.implode(',', array_keys($field->options)),
                    $field->slug));
        }

        return $rules;
    }

    /**
     * Get validation rules for custom field
     *
     * @param CustomField $field
     * @return string
     */
    private function getFieldValidationRules(CustomField $field)
    {
        $rules = $field->validation;

        if ($field->required) {
            $rules = $this->addRule($rules, 'required');
        }

        switch ($field->data_type) {
            case ('text'):

                break;
            case ('date'):
                $rules = $this->addRule($rules, 'date');
                break;
            case ('datetime'):
                $rules = $this->addRule($rules, 'date');
                break;
            case ('boolean'):
                $rules = $this->addRule($rules, 'boolean');
                break;
            case ('select'):
                $rules = $this->addRule($rules, 'in:'.implode(',', array_keys($field->options)));
                break;
            case ('multiselect'):
                $rules = $this->addRule($rules, 'array');
                break;
        }

        return $rules;
    }

    /**
     * Add rule to rulestring if it doesn't exist
     *
     * @param string $rulesString
     * @param string $rule
     * @return string
     */
    private function addRule($rulesString, $rule)
    {
        if (strpos($rulesString, $rule) === false) {
            return $rulesString .= (strlen($rulesString) > 0 ? '|' : '').$rule;
        }

        return $rulesString;
    }

    /**
     * Get rules for an array
     *
     * @param array $data
     * @param string $rule
     * @param string $parent
     * @return array
     */
    private function getArrayRules($data, $rule, $parent)
    {
        $rules = [];

        if (empty($data) || ! is_array($data)) {
            return $rules;
        }

        foreach ($data as $key => $value) {
            $rules["{$parent}.{$key}"] = $rule;
        }

        return $rules;
    }
}
