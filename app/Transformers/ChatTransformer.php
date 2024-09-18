<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Tobuli\Entities\Chat;

class ChatTransformer extends TransformerAbstract {

    protected $defaultIncludes = [
        'messages'
    ];

    public function transform(Chat $entity)
    {
        return [
            'id'    => $entity->id,
            'hash'  => $entity->roomHash
        ];
    }

    public function includeMessages(Chat $chat) {
        $messages = $chat->getLastMessages()->setPath(route('tracker.chat.messages'));

        return $this
            ->collection($messages, new ChatMessageTransformer)
            ->setPaginator(new IlluminatePaginatorAdapter($messages));
    }

    public function includeParticipants(Chat $chat) {
        return $this->collection($chat->participants, new ChatParticipantTransformer, false);
    }
}