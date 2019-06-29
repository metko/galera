<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class UserDoesntExist extends InvalidArgumentException
{
    public static function create($user)
    {
        return new static("Can’t add a user on the conversation because it doesn\'t exist. User id: {$user}");
    }
}
