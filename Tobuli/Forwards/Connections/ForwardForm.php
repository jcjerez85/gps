<?php


namespace Tobuli\Forwards\Connections;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\InputFields\AbstractField;

abstract class ForwardForm implements Arrayable, \Tobuli\Forwards\ForwardForm
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @return array
     */
    abstract public function getAttributes();

    /**
     * ForwardForm constructor.
     * @param array|null $config
     */
    public function __construct($config = null)
    {
        $this->setConfig($this->getDefaults());

        if ($config)
            $this->setConfig($config);
    }

    /**
     * @return array
     */
    protected function getDefaults()
    {
        return [];
    }

    /**
     * @param $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string $key
     * @return array|\ArrayAccess|mixed
     */
    protected function get(string $key)
    {
        return Arr::get($this->config, $key);
    }

    /**
     * @param array $input
     * @throws ValidationException
     */
    public function validate(array $input)
    {
        $rules = [];

        /** @var AbstractField $parameter */
        foreach ($this->getAttributes() as $attribute) {
            if (empty($attribute->getValidation())) {
                continue;
            }

            $rules[$attribute->getName()] = $attribute->getValidation();
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->messages());
        }
    }

    public function toArray()
    {
        return [
            'title'      => self::getTitle(),
            'attributes' => $this->getAttributes(),
        ];
    }
}