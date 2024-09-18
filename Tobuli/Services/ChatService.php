<?php

namespace Tobuli\Services;

use Tobuli\Entities\Chat;
use Tobuli\Entities\ChatMessage;
use Tobuli\Entities\ChatParticipant;
use Tobuli\Entities\ChattableInterface;

class ChatService
{
    const MAX_UNREAD_MSG = 9;

    public function getUnreadMessageCount(ChattableInterface $chattable)
    {
        $count = ChatMessage::unread()
            ->whereChattable($chattable)
            ->whereSender($chattable, true)
            ->limit(self::MAX_UNREAD_MSG + 1)
            ->count();

        if ($count > self::MAX_UNREAD_MSG) {
            $count = self::MAX_UNREAD_MSG . '+';
        }

        return $count;
    }

    public function markAsRead(Chat $chat, ChattableInterface $chattable)
    {
        ChatParticipant::where('chat_id', $chat->id)->byEntity($chattable)->update([
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }


}