<?php namespace Tobuli\Helpers\Templates;

use Illuminate\Support\Str;
use Tobuli\Helpers\Templates\Builders\TemplateBuilder;

class TemplateManager
{
    public function loadTemplateBuilder($template): TemplateBuilder
    {
        $builder = 'Tobuli\Helpers\Templates\Builders\\' . Str::studly($template) . 'Template';

        if ( ! class_exists($builder))
            throw new \Exception('Not found template builder for template');

        return new $builder();
    }
}