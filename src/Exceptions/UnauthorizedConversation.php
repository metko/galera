<?php

namespace Metko\Galera\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedConversation extends HttpException
{
    public static function create()
    {
        return new static(403, 'You can write in this conversation');
    }
}
