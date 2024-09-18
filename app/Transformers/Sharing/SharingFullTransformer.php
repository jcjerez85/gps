<?php

namespace App\Transformers\Sharing;

use Tobuli\Entities\Sharing;
use Formatter;

class SharingFullTransformer extends AbstractSharingTransformer
{
    public function transform(Sharing $entity)
    {
        return [
            'id'                        => (int)$entity->id,
            'user_id'                   => (int)$entity->user_id,
            'name'                      => $entity->name,
            'hash'                      => $entity->hash,
            'expiration_date'           => Formatter::time()->human($entity->expiration_date),
            'expiration_date_real'      => $entity->expiration_date,
            'active'                    => $entity->active,
            'delete_after_expiration'   => $entity->delete_after_expiration,
        ];
    }
}