<?php

namespace Tobuli\Exporters\Util;

trait ParseValueTrait
{
    protected function parseValues($item, array $attributes): array
    {
        $values = [];

        foreach ($attributes as $attribute) {
            $values[] = $item->$attribute;
        }

        return $values;
    }
}
