<?php

namespace Tobuli\Traits;

trait AttributesRelationsGetter
{
    public function getRelationsForAttributes(array $attributes): array
    {
        if (!isset($this->attributesRelations)) {
            throw new \LogicException('`$this->attributesRelations` must be initialized');
        }

        $relations = [];

        foreach ($attributes as $attribute) {
            $attributeRelations = $this->attributesRelations[$attribute] ?? null;

            if (!is_array($attributeRelations)) {
                continue;
            }

            foreach ($attributeRelations as $relation) {
                if (!in_array($relation, $relations)) {
                    $relations[] = $relation;
                }
            }

        }

        return $relations;
    }
}