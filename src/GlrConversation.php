<?php

namespace Metko\Galera;

use Tests\Models\User;
use Metko\Galera\Facades\Galera;
use Illuminate\Database\Eloquent\Model;
use Metko\Galera\Exceptions\UserAlreadyInConversation;

class GlrConversation extends Model
{
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(GlrMessage::class, 'conversation_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'glr_conversation_user', 'conversation_id', 'user_id');
    }

    public function add($user)
    {
        $user = $this->getUser($user);

        return $this->participants()->attach($user);
    }

    public function addMany(array $users)
    {
        $users = collect($users)->map(function ($user) {
            $user = $this->getUser($user);

            return $user->id;
        });
        //dd($users);

        return $this->participants()->syncWithoutDetaching($users);
    }

    public function getUser($user)
    {
        $user = Galera::isValidUser($user);
        if ($this->hasUser($user)) {
            throw UserAlreadyInConversation::create($user->id);
        }

        return $user;
    }

    public function hasUser($user)
    {
        return $this->participants->contains('id', $user->id);
    }

    public function remove($user)
    {
        $user = Galera::isValidUser($user);

        $this->participants()->detach($user);
    }

    public function close()
    {
        return $this->update(['closed' => true]);
    }

    public function isClosed()
    {
        return $this->closed;
    }
}
