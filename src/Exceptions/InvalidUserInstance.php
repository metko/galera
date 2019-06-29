<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class InvalidUserInstance extends InvalidArgumentException
{
    public static function create($user)
    {
        return new static("User must be an instane of App\User; ");
    }
}
