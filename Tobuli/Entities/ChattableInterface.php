<?php

namespace Tobuli\Entities;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ChattableInterface
{
    public function chats();
    public function messages();
    public function getChatableType();
    public function getChatableName();
}