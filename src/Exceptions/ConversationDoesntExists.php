<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class ConversationDoesntExists extends InvalidArgumentException
{
    public static function create($conversation)
    {
        return new static("This conversation doesnt exists. Conversation: {$conversation}");
    }
}
