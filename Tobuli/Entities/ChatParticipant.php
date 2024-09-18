<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.3.19
 * Time: 13.34
 */

namespace Tobuli\Entities;


use Illuminate\Database\Eloquent\Relations\Relation;

class ChatParticipant extends AbstractEntity
{
    protected $fillable = ['chat_id', 'chattable_id', 'chattable_type', 'read_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public function chattable() {
        return $this->morphTo();
    }

    public function scopeByEntity($query, ChattableInterface $entity)
    {
        return $query->where('chattable_id', '=', $entity->id)
            ->where('chattable_type', array_search(get_class($entity), Relation::morphMap()));
    }

    public function isUser()
    {
        return $this->chattable_type == array_search(User::class, Relation::morphMap());
    }

    public function isDevice()
    {
        return $this->chattable_type == array_search(Device::class, Relation::morphMap());
    }
}
