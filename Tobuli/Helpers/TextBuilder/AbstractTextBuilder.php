<?php

namespace Tobuli\Helpers\TextBuilder;

abstract class AbstractTextBuilder
{
    public function build(string $text, $args): string
    {
        $valueMap = $this->getValueMap($args);

        foreach ($valueMap as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    abstract protected function getValueMap($args): array;
}