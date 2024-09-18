<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Tobuli\Entities\ChatMessage;

class ChatMessageTransformer extends TransformerAbstract
{
    public function transform(ChatMessage $entity)
    {
        return [
            'id'          => $entity->id,
            'chat_id'     => $entity->chat_id,
            'chat_url'    => $entity->chat_url,
            'content'     => $entity->content,
            'type'        => $entity->type,
            'sender_id'   => $entity->sender_id,
            'sender_name' => $entity->sender_name,
            'chattable_id'=> $entity->chattable_id,
            'created_at'  => $entity->created_at,
        ];
    }
}