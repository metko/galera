<?php

namespace Metko\Galera;

use Tests\Models\User;
use Metko\Galera\Exceptions\UserDoesntExist;

class GaleraClass
{
    public function isValidUser($user)
    {
        if (!$user instanceof User) {
            if (is_numeric($user) || is_integer($user)) {
                if (!$user = User::whereId($user)->first()) {
                    throw UserDoesntExist::create($user);
                }
            }
        }

        return $user;
    }
}
