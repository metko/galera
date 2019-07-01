<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class MessageDoesntBelongsToUser extends InvalidArgumentException
{
    public static function create($message)
    {
        return new static("Ce message n'appartient pas a l'utilisateur : {$message} ");
    }
}
