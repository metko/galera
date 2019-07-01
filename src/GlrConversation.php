<?php

namespace Metko\Galera;

use Tests\Models\User;
use Metko\Galera\Facades\Galera;
use Illuminate\Database\Eloquent\Model;
use Metko\Galera\Exceptions\CantRemoveUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Metko\Galera\Exceptions\UserAlreadyInConversation;

class GlrConversation extends Model
{
    use SoftDeletes;

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
            return $this->getUser($user)->id;
        });

        return $this->participants()->syncWithoutDetaching($users);
    }

    public function getUser($user)
    {
        if ($this->hasUser($user = Galera::isValidUser($user))) {
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
        if ($this->participants->count() <= 2) {
            throw CantRemoveUser::create('Impossible de supprimer l\'utilisateur car il faut être au mimnimum deux pour communiquer ;)');
        }

        return $this->participants()->detach(Galera::isValidUser($user));
    }

    public function close()
    {
        return $this->update(['closed' => true]);
    }

    public function isClosed()
    {
        return $this->closed;
    }

    public function clear()
    {
        return GlrMessage::where('conversation_id', $this->id)->delete();
    }
}
