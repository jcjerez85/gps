<?php

namespace Tobuli\InputFields;

use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractField implements Arrayable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|int|string
     */
    protected $index = null;

    /**
     * @var string
     */
    protected $title;
    protected $default;
    protected $description = '';
    protected $validation = [];
    protected $required = false;
    protected $additionalParameters = [];
    protected $template = '';

    abstract public function getType(): string;
    abstract public function render();

    public function __construct(string $name, string $title, $default = null)
    {
        $this->setName($name);
        $this->setTitle($title);
        $this->setDefault($default);
    }

    public function toArray(): array
    {
        return [
            'name'          => $this->getName(),
            'html_name'     => $this->getHtmlName(),
            'title'         => $this->getTitle(),
            'type'          => $this->getType(),
            'default'       => $this->getDefault(),
            'description'   => $this->getDescription(),
            'validation'    => $this->getValidation(),
            'required'      => $this->isRequired(),
        ] + $this->additionalParameters;
    }

//    abstract public function toHtml(array $options);



    public function getHtmlName(): string
    {
        return $this->index !== null
            ? $this->name . '[' . $this->index . ']'
            : $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default): self
    {
        $this->default = $default;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValidation(): string
    {
        return implode('|', $this->validation);
    }

    public function setValidation($validation): self
    {
        if (!is_array($validation)) {
            $validation = explode('|', $validation);
        }

        foreach ($validation as $rule) {
            $this->addValidation($rule);
        }

        return $this;
    }

    public function addValidation(string $validation): self
    {
        $key = $this->extractValidationKey($validation);

        $this->validation[$key] = $validation;

        return $this;
    }

    public function removeValidation(string $validation): self
    {
        $key = $this->extractValidationKey($validation);

        unset($this->validation[$key]);

        return $this;
    }

    private function extractValidationKey(string $validation): string
    {
        return explode(':', $validation)[0];
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required = true): self
    {
        $this->required = $required;

        $required
            ? $this->addValidation('required')
            : $this->removeValidation('required');

        return $this;
    }

    public function getAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    public function getAdditionalParameter($key)
    {
        return $this->additionalParameters[$key] ?? null;
    }

    public function setAdditionalParameters(array $additionalParameters): self
    {
        $this->additionalParameters = $additionalParameters;

        return $this;
    }

    public function addAdditionalParameter($key, $value): self
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    public function removeAdditionalParameter($key): self
    {
        unset($this->additionalParameters[$key]);

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function renderFormGroup(array $options = [])
    {
        $label = \Form::label($this->getHtmlName(), $this->getTitle());
        $input = $this->render($options);

        if ($description = $this->getDescription()) {
            $description = "<div>$description</div>";
        }

        return "<div class='form-group'>$label $input $description</div>";
    }
}