<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class UserAlreadyInConversation extends InvalidArgumentException
{
    public static function create($user)
    {
        return new static("User already in the conversation. User id: {$user}");
    }
}
