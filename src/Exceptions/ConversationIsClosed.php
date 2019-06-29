<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class ConversationIsClosed extends InvalidArgumentException
{
    public static function create($conversation)
    {
        return new static("This conversation is closed. Conversation: {$conversation}");
    }
}
