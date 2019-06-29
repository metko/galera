<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class MessageDoesntExist extends InvalidArgumentException
{
    public static function create($message)
    {
        return new static("Vous ne pouvez pas répondre a ce message car il n'existe pas ou plus. Mesage : {$message} ");
    }
}
