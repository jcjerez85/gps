<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.4.5
 * Time: 12.00
 */

namespace Tobuli\Entities;



class ChatMessageToChat extends AbstractEntity {

    protected $fillable = ['chat_id', 'message_id'];

}