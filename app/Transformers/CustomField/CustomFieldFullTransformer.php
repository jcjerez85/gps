<?php

namespace App\Transformers\CustomField;

use Tobuli\Entities\CustomField;

class CustomFieldFullTransformer extends CustomFieldTransformer
{

    /**
     * @param CustomField $entity
     * @return array|null
     */
    public function transform($entity)
    {
        if (! $entity) {
            return null;
        }

        return [
            'id'          => (int) $entity->id,
            'model'       => (string) $entity->model,
            'title'       => (string) $entity->title,
            'data_type'   => (string) $entity->data_type,
            'options'     => toOptions($entity->options ?? []),
            'default'     => (string) $entity->default,
            'slug'        => (string) $entity->slug,
            'required'    => (int) $entity->required,
            'validation'  => (string) $entity->validation,
            'description' => (string) $entity->description,
        ];
    }
}
