<?php


namespace Tobuli\Forwards;


interface ForwardForm
{
    public function getAttributes();
    public function setConfig(array $config);
    public function validate(array $input);
}