<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.19
 * Time: 13.37
 */

namespace Tobuli\Entities;

use App\Events\NewMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use CustomFacades\Server;

class ChatMessage extends AbstractEntity {

    const TYPE_TEXT = 1;
    const TYPE_PICTURE = 2;

    protected $fillable = ['sender_id', 'chat_id', 'content', 'type' ];
    protected $hidden = ['created_at', 'updated_at', 'sender'];

    protected $appends = ['sender_name', 'chat_url', 'chattable_id'];

    public function chat() {
        return $this->hasOne(Chat::class, 'id','chat_id');
    }

    public function sender() {
        return $this->hasOne(ChatParticipant::class, 'id', 'sender_id');
    }

    public function scopeSince($query, $time)
    {
        return $query->where('created_at', '>', date('Y-m-d H:i:s', $time));
    }

    public function getChatUrlAttribute() {
        return str_replace('http://localhost', Server::url(), route('chat.get', [$this->chat_id]));
    }

    public function getSenderNameAttribute() {
        if ( ! $this->sender) {
            return 'N/A';
        }

        if ( ! $this->sender->chattable) {
            return 'N/A';
        }

        return $this->sender->chattable->getChatableName();
    }

    public function getChattableIdAttribute() {
        if ( ! $this->sender) {
            return null;
        }

        if ( ! $this->sender->chattable) {
            return null;
        }

        return $this->sender->chattable->id;
    }

    public function isMyMessage($entity)
    {
        if ( ! $this->sender->chattable) {
            return false;
        }

        if (array_search(get_class($entity), Relation::morphMap()) != $this->sender->chattable_type) {
            return false;
        }

        if ($this->sender->chattable->id != $entity->id) {
            return false;
        }

        return true;
    }

    public function setFrom($entity) {
        $this->sender_id = $entity->chats()
            ->where('chat_participants.chat_id', $this->chat->id)
            ->first()
            ->id;

        return $this;
    }

    public function setTo($entities = null, $chat = null) {

        if ($chat) {
            $this->chat_id = $chat->id;

            return $this;
        }

        $chat = Chat::getRoom($entities);

        $this->chat_id = $chat->id;

        return $this;
    }

    public function setContent( $content, $type = self::TYPE_TEXT )
    {
        $this->content = $content;
        $this->type = $type;

        return $this;
    }

    public function send() {

        $this->save();

        event(new NewMessage($this));
    }

    public function scopeWhereChattable(Builder $query, Model $chattable)
    {
        $type = array_search(get_class($chattable), Relation::morphMap());

        return $query->innerJoinParticipants()
            ->where('cpa.chattable_id', '=', $chattable->id)
            ->where('cpa.chattable_type', '=', $type);
    }

    public function scopeInnerJoinParticipants(Builder $query)
    {
        if ($query->isJoined('chat_participants AS cpa')) {
            return $query;
        }

        return $query->join('chat_participants AS cpa', function ($join) {
            $join->on('cpa.chat_id', '=', 'chat_messages.chat_id');
        });
    }

    public function scopeUnread(Builder $query)
    {
        return $query->innerJoinParticipants()
            ->where('chat_messages.created_at', '>', \DB::raw('cpa.read_at'));
    }

    public function scopeWhereSender(Builder $query, Model $sender, bool $invert = false)
    {
        $method = $invert ? 'whereNotIn' : 'whereIn';

        return $query->innerJoinParticipants()
            ->$method('chat_messages.sender_id', function (\Illuminate\Database\Query\Builder $query) use ($sender) {
                $type = array_search(get_class($sender), Relation::morphMap());

                $query->select('id')
                    ->from('chat_participants')
                    ->where('chat_participants.chattable_id', '=', $sender->id)
                    ->where('chat_participants.chattable_type', '=', $type);
            });
    }
}