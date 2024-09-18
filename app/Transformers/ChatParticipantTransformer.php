<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Tobuli\Entities\ChatParticipant;

class ChatParticipantTransformer extends TransformerAbstract {

    public function transform(ChatParticipant $entity)
    {
        return [
            'id'           => $entity->id,
            'name'         => $entity->chattable->getChatableName(),
            'type'         => $entity->chattable->getChatableType(),
            'chattable_id' => $entity->chattable_id
        ];
    }
}