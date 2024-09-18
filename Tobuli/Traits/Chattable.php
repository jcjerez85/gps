<?php
namespace Tobuli\Traits;

use Tobuli\Entities\ChatMessage;
use Tobuli\Entities\ChatParticipant;
use Tobuli\Entities\User;


use App\Exceptions\ResourseNotFoundException;
use App\Exceptions\PermissionException;

trait Chattable
{
    /**
     * Get the entity's chats.
     */
    public function chats() {
        return $this->morphMany(ChatParticipant::class, 'chattable')->orderBy('created_at', 'desc');
    }

    public function messages() {
        return $this->hasManyThrough(
            ChatMessage::class,
            ChatParticipant::class,
            'chattable_id',
            'sender_id',
            'id',
            'id'
        )->where('chat_participants.chattable_type', '=', $this->getChatableType());
        return $this->hasMany(ChatMessage::class, 'sender_id', 'id');
    }

    public function getChatableType()
    {
        return lcfirst(class_basename(self::class));
    }

    public function getChatableName()
    {
        if ($this instanceof User)
            return $this->email;

        return $this->name;
    }

    public function toChatableObject()
    {
        return [
            'id'   => $this->id,
            'name' => $this->getChatableName(),
            'type' => $this->getChatableType()
        ];
    }

    public function chatableObjects()
    {
        $chatables = [];

        if ($this instanceof User)
        {
            $devices = $this->devices()->with('traccar')->get();

            foreach ($devices as $device)
            {
                if (!$device->canChat())
                    continue;

                $chatables[] = $device->toChatableObject();
            }

            foreach ($this->subusers as $subuser)
            {
                $chatables[] = $subuser->toChatableObject();
            }

        } else {
            foreach ($this->users as $user)
            {
                $chatables[] = $user->toChatableObject();
            }
        }

        return $chatables;
    }
}