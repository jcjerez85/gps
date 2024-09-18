<?php namespace Tobuli\Helpers\Templates\Builders;

use Appearance;
use Formatter;
use Carbon\Carbon;
use Tobuli\Entities\EmailTemplate;

abstract class TemplateBuilder
{
    protected $user;

    abstract protected function variables($item);
    abstract protected function placeholders();

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function buildTemplate($template, $data = null)
    {
        $variables = $this->variables($data);

        if ($template instanceof EmailTemplate)
            $variables = array_merge($variables, $this->_variables());

        return [
            'subject' => strtr($template->title, $variables),
            'body'    => strtr($template->note, $variables),
        ];
    }

    public function getPlaceholders($template)
    {
        $placeholders = $this->placeholders();

        if ($template instanceof EmailTemplate)
            $placeholders =  array_merge($placeholders, $this->_placeholders());

        return $placeholders;
    }

    protected function _placeholders()
    {
        return [
            '[logo]'     => 'Server logo',
            '[datetime]' => 'Current Date&Time',
        ];
    }

    protected function _variables()
    {
        return [
            '[logo]'     => '<img src="'.Appearance::getAssetFileUrl('logo').'" alt="Logo" title="Logo" />',
            '[datetime]' => Formatter::time()->human( Carbon::now() ),
        ];
    }
}