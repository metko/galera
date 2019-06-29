<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class CantRemoveUser extends InvalidArgumentException
{
    public static function create($message = null)
    {
        if (!$message) {
            $message = 'Impossible de retirer l\'utilisateur de la conversation';
        }

        return new static($message);
    }
}
