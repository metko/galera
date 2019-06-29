<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class MessageInvalidType extends InvalidArgumentException
{
    public static function create()
    {
        return new static('Le message doit être une instance de GlrMessage:class.');
    }
}
