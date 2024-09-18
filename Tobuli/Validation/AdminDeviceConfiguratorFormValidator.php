<?php namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;

class AdminDeviceConfiguratorFormValidator extends Validator {

    /**
     * @var array Validation rules for the test form, they can contain in-built Laravel rules or our custom rules
     */
    public $rules = [
        'create' => [
            'brand' => 'required',
            'commands' => 'required|array',
        ],
        'update' => [
            'id' => 'required|exists:device_config,id',
            'brand' => 'required',
            'commands' => 'required|array',
        ],
    ];

    public function validate($name, array $data, $id = NULL)
    {
        if (isset($this->rules[$name]) && array_key_exists('commands', $this->rules[$name])) {
            foreach (array_keys($this->rules[$name]) as $key) {
                if (strpos($key, 'commands.') !== false) {
                    unset($this->rules[$name][$key]);
                }
            }

            if (isset($data['commands']) && is_array($data['commands'])) {
                foreach ($data['commands'] as $key => $command) {
                    $this->rules[$name]['commands.'.$key] = 'required|string';
                }
            }
        }

        parent::validate($name, $data, $id);
    }
}
