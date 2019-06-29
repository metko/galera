<?php

namespace Metko\Galera\Exceptions;

use InvalidArgumentException;

class InsufisantParticipant extends InvalidArgumentException
{
    public static function create()
    {
        return new static('A conversation must have at least two participants.');
    }
}
