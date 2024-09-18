<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.19
 * Time: 13.37
 */

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;


class Chat extends AbstractEntity {

    public function participants() {
        return $this->hasMany(ChatParticipant::class, 'chat_id', 'id');
    }

    public function messages() {
        return $this->hasMany(ChatMessage::class)->with(['sender'])->orderBy('id','desc');
    }

    public function getLastMessages()
    {
        return $this->messages()->reversePaginate();
    }

    public function getTitleAttribute() {
        $title = [];

        foreach ($this->participants as $participant) {
            if ( ! $participant->chattable) {
                continue;
            }

            $title[] = ($participant->chattable->name ? $participant->chattable->name : $participant->chattable->email );
        }

        return implode(' | ', $title);
    }

    public function addParticipant($participant): ChatParticipant
    {
        return $this
            ->participants()
            ->firstOrCreate([
                'chattable_id' => $participant->id,
                'chattable_type' => array_search(get_class($participant), Relation::morphMap())
            ]);
    }

    public function addParticipants($participants)
    {
        foreach ($participants as $participant) {
            $this->addParticipant($participant);
        }
    }

    public function scopeGetByDevice($query, Device $device)
    {
        return $query->getByParticipants([$device]);
    }

    public function scopeGetByParticipants($query, $participants)
    {
        foreach ($participants as $entity) {
            $query->whereHas('participants', function ($query) use ($entity) {
                $query->where('chattable_id', '=', $entity->id)
                    ->where('chattable_type', array_search(get_class($entity), Relation::morphMap()));
            });
        }

        return $query;
    }

    public static function getRoom($participants) {
        $chat = self::getByParticipants($participants)->first();

        if ( ! $chat) {
            $chat = self::createRoom($participants);
        }

        return $chat;
    }

    public static function getRoomByDevice(Device $device) {
        $chat = self::getByDevice($device)->first();

        if ( ! $chat) {
            $participants = new Collection();
            $participants->push($device);
            $participants = $participants->merge($device->users);
            $participants = $participants->all();

            $chat =  self::createRoom($participants);
        }

        return $chat;
    }

    private static function createRoom($participants) {
        $chat = new Chat();
        $chat->save();

        $chat->addParticipants($participants);

        return $chat;
    }

    public function getRoomHashAttribute() {
        return md5('message_for_'. $this->id);
    }

}