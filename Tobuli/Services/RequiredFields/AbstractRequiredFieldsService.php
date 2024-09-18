<?php

namespace Tobuli\Services\RequiredFields;

use Illuminate\Support\Str;

abstract class AbstractRequiredFieldsService
{
    public function getRules(): array
    {
        $key = $this->getSettingsKey();
        $rules = settings('extra_required_fields.' . $key) ?: [];

        return array_filter($rules, function ($field) {
            $method = 'is' . ucfirst(Str::camel($field)) . 'Enabled';

            return !method_exists($this, $method) || $this->$method();
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getSettingsKey(): string
    {
        return Str::snake(substr(class_basename(static::class), 0, -21));
    }
}