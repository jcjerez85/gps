<?php namespace Tobuli\Validation;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Entities\CustomField;
use Tobuli\Exceptions\ValidationException;

class CustomFieldFormValidator extends Validator
{
    private $availableRules = [];

    public $rules = [
        'create' => [
            'title' => 'required',
            'required' => 'required|boolean',
            'description' => 'string|max:255',
        ],
        'update' => [
            'title' => 'required',
            'required' => 'required|boolean',
            'description' => 'string|max:255',
        ],
    ];

    public function __construct(IlluminateValidator $validator)
    {
        $this->availableRules = $this->getAvailableValidationMethods();
        parent::__construct($validator);
    }

    public function validate($name, array $data, $id = NULL)
    {
        if (in_array($data['data_type'] ?? '', ['select', 'multiselect'])) {
            $this->validateOptions($data);
        }

        $this->validateValidationRules($data);
        $this->generateRules($name, $data);

        parent::validate($name, $data, $id);

        $this->validateSlugInModel($data);

        if ($name == 'update') {
            $this->validateTypeAndSlugChanges($data);
        }
    }

    /**
     * Set default value rules if necessary
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    private function generateDefaultValueRule($name, $data)
    {
        if (! isset($data['default'])) {
            return;
        }

        if (in_array($data['data_type'], ['select', 'multiselect'])) {
            $this->setDefaultSelectRules($name, $data);

            return;
        }

        $this->setDefaultRule($name, $data);
    }

    /**
     * Set default value rule when custom field is select/multiselect
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    private function setDefaultSelectRules($name, $data)
    {
        if (! isset($data['options'])) {
            return;
        }

        $rule = 'in:'.implode(',', array_keys($data['options']));

        if ($data['data_type'] == 'select' || ! is_array($data['default'])) {
            $this->rules[$name]['default'] = $rule;

            return;
        }

        $this->rules[$name]['default'] = 'array';

        foreach ($data['default'] as $key => $value) {
            $this->rules[$name]["default.{$key}"] = $rule;
        }
    }

    /**
     * Set default value rule for custom field
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    private function setDefaultRule($name, $data)
    {
        $rule = '';

        switch ($data['data_type']) {
            case ('date'):
            case ('datetime'):
                $rule = 'date';
                
                break;
            case ('boolean'):
                $rule = 'boolean';

                break;
            default:
                break;
        }

        if (empty($rule)) {
            return;
        }

        $this->rules[$name]['default'] = $rule;
    }

    /**
     * Generate general rules for slug and data_type. Also generate rules for custom fields with default values
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    private function generateRules($name, $data)
    {
        // slug is only unique to this model type, but not to all others
        $customFieldId = $data['id'] ?? 'NULL';

        $this->rules[$name]['slug'] = 'required|unique:custom_fields,slug,'.$customFieldId.',id,model,'.$data['model'];
        $this->rules[$name]['data_type'] = 'required|in:'.implode(',', array_keys(CustomField::getDataTypes()));

        $this->generateDefaultValueRule($name, $data);
    }

    /**
     * Validate select/multiselect options
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    private function validateOptions($data)
    {
        $rules = [];
        $rules['options'] = 'required|array';

        $messages = [
            'options' => trans('validation.required', ['attribute' => trans('validation.attributes.option')]),
        ];

        foreach ($data['options'] ?? [] as $key => $option) {
            $rules["options.{$key}"] = 'required|string';
            $messages["options.{$key}"] = trans('validation.required', ['attribute' => trans('validation.attributes.option')]);
        }

        $validator = \Validator::make($data, $rules);
        $validator->setCustomMessages($messages);

        if ($validator->fails()) {
            throw new ValidationException(array_slice($messages, 0, 1));
        }
    }

    /**
     * Validate that data_type and slug hasn't been changed
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    private function validateTypeAndSlugChanges($data)
    {
        $unchangeableFields = ['data_type', 'slug'];
        $item = CustomField::find($data['id']);
        $item->fill($data);
        $dirtyFields = array_keys($item->getDirty());
        $unchangeableFields = array_intersect($dirtyFields, $unchangeableFields);

        if (empty($unchangeableFields)) {
            return;
        }

        if (! $item->customValues()->count()) {
            return;
        }

        $errors = [];

        foreach ($unchangeableFields as $field) {
            $errors[$field] = trans('validation.cant_update_custom_field',
                ['field' => trans('validation.attributes.'.$field)]);
        }

        throw new ValidationException($errors);
    }

    /**
     * Validate that slug is unique for model
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     * @throws \Exception
     */
    private function validateSlugInModel($data)
    {
        if (! isset($data['model'])) {
            throw new ValidationException(dontExist(trans('front.model')));
        }

        $modelClass = Relation::morphMap()[$data['model']] ?? null;

        if (! isset($modelClass)) {
            throw new \Exception('no morph relation');
        }

        $model = new $modelClass();
        $modelDbFields = Schema::getColumnListing($model->getTable());
        $modelMethods = get_class_methods($modelClass);
        $accessorName = 'get'.Str::studly($data['slug']).'Attribute';

        if (array_search($data['slug'], $modelDbFields) !== false
            || array_search($accessorName, $modelMethods) !== false) {
            throw new ValidationException(['slug' => trans(
                'validation.unique',
                ['attribute' => $data['slug']]
            )]);
        }
    }

    /**
     * Validate that validation rules are correct
     *
     * @param array $data
     * @return void
     */
    private function validateValidationRules($data)
    {
        if (! isset($data['validation']) || empty($data['validation'])) {
            return ;
        }

        $fieldRules = explode('|', $data['validation']);
        $fieldRules = array_map(function($value) {
            $parts = explode(':', $value);

            return [
                'name' => $parts[0],
                'params' => $parts[1] ?? false,
            ];
        }, $fieldRules);

        $this->validateUnsupportedRules($fieldRules);
        $this->validateRulesParams($fieldRules);
    }

    /**
     * Validate that parameterized rules has parameters set
     *
     * @param array $fieldRules
     * @return void
     * @throws ValidationException
     */
    private function validateRulesParams($fieldRules)
    {
        $rulesWithWrongParams = [];

        foreach ($fieldRules as $fieldRule) {
            $availableRule = Arr::first($this->availableRules, function($v) use($fieldRule) {
                return $fieldRule['name'] == $v['name'];
            });

            if ($fieldRule['params'] == $availableRule['params']) {
                continue;
            }

            if ($fieldRule['params'] !== false && $availableRule['params'] !== false) {
                continue;
            }

            $rulesWithWrongParams[] = $fieldRule;
        }

        if ($rulesWithWrongParams) {
            throw new ValidationException([
                'validation' => trans('validation.unsupported_parameterized_rules')
                    .implode(', ', array_column($rulesWithWrongParams, 'name'))
            ]);
        }
    }

    /**
     * Validate that every listed rule is in laravel's supported list or is in extended rueles list
     *
     * @param array $fieldRules
     * @return void
     * @throws ValidationException
     */
    private function validateUnsupportedRules($fieldRules)
    {
        $unsupportedRules = array_diff(array_column($fieldRules, 'name'), array_column($this->availableRules, 'name'));

        if ($unsupportedRules) {
            throw new ValidationException([
                'validation' => trans('validation.unsupported_rules')
                    .implode(', ', $unsupportedRules)
            ]);
        }
    }

    /**
     * Get list of available validation methods
     *
     * @return array
     */
    private function getAvailableValidationMethods()
    {
        $validator = \Validator::make([], []);

        return array_merge($this->getDefaultValidationMethods($validator),
            $this->getExtendedValidationMethods($validator));
    }

    /**
     * Get list of extended validation methods
     *
     * @param Validator $validator
     * @return array
     */
    private function getExtendedValidationMethods($validator)
    {
        $extendedMethods = [];

        foreach ($validator->extensions as $value => $callback) {
            if (is_string($callback)) {
                list($class, $method) = explode('@', $callback);
                $reflectionClass = new \ReflectionClass($class);
                $method = $reflectionClass->getMethod($method);
            } else if (is_object($callback) && get_class($callback) == 'Closure') {
                $method = new \ReflectionFunction($callback);
            }

            $params = $method->getParameters();
            $last = array_pop($params);

            $extendedMethods[] = [
                'name' => $value,
                'params' => ($last && $last->name == 'parameters'),
            ];
        }

        return $extendedMethods;
    }

    /**
     * Get list of laravel's validation  methods
     *
     * @param Validator $validator
     * @return array
     */
    private function getDefaultValidationMethods($validator)
    {
        $result = [];
        $reflectionClass = new \ReflectionClass($validator);
        $methods = $reflectionClass->getMethods();

        //filter down to just the rules
        $methods = array_filter($methods, function($value) {
            if ($value->name == 'validate') {
                return false;
            }

            return strpos($value->name, 'validate') === 0;
        });

        //get the rule name, also if it has parameters
        foreach ($methods as $value) {
            $name = preg_replace('%^validate%','',$value->name); 
            $name = Str::snake($name);

            $params = $value->getParameters();
            $last = array_pop($params);

            $result[] = [
                'name' => $name,
                'params' => ($last && $last->name == 'parameters'),
            ];
        }

        return $result;
    }
}
