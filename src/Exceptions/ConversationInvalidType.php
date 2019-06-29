<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class ConversationInvalidType extends InvalidArgumentException
{
    public static function create($conversation)
    {
        $type = gettype($conversation);

        return new static("Conversation must be an integer or a string, {$type} given.");
    }
}
