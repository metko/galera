<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class MessageDoesntBelongsToConversation extends InvalidArgumentException
{
    public static function create($message)
    {
        return new static("Vous ne pouvez pas répondre a ce message car il n'appartient pas a cette conversation. Mesage : {$message} ");
    }
}
