<?php

namespace Metko\Galera;

use Metko\Galera\Facades\Galera;
use Illuminate\Database\Eloquent\Model;
use Metko\Galera\Exceptions\CantRemoveUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Metko\Galera\Exceptions\UserAlreadyInConversation;

class GlrConversation extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * __construct.
     *
     * @param mixed $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('galera.table_prefix').'conversations';
    }

    /**
     * messages.
     */
    public function messages()
    {
        return $this->hasMany(GlrMessage::class, 'conversation_id');
    }

    /**
     * participants.
     */
    public function participants()
    {
        return $this->belongsToMany(config('galera.user_class'), config('galera.table_prefix').'conversation_user', 'conversation_id', 'user_id');
    }

    /**
     * participants.
     */
    public function status()
    {
        return $this->hasMany(GlrMessageNotification::class, 'conversation_id');
    }

    /**
     * add.
     *
     * @param mixed $user
     */
    public function add($user)
    {
        $user = $this->getUser($user);

        return $this->participants()->attach($user);
    }

    /**
     * addMany.
     *
     * @param mixed $users
     */
    public function addMany(array $users)
    {
        $users = collect($users)->map(function ($user) {
            return $this->getUser($user)->id;
        });

        return $this->participants()->syncWithoutDetaching($users);
    }

    /**
     * getUser.
     *
     * @param mixed $user
     */
    public function getUser($user)
    {
        if ($this->hasUser($user = Galera::isValidUser($user))) {
            throw UserAlreadyInConversation::create($user->id);
        }

        return $user;
    }

    /**
     * hasUser.
     *
     * @param mixed $user
     */
    public function hasUser($user)
    {
        return $this->participants->contains('id', $user->id);
    }

    /**
     * remove.
     *
     * @param mixed $user
     */
    public function remove($user)
    {
        if ($this->participants->count() <= 2) {
            throw CantRemoveUser::create('Impossible de supprimer l\'utilisateur car il faut être au mimnimum deux pour communiquer ;)');
        }

        return $this->participants()->detach(Galera::isValidUser($user));
    }

    /**
     * close.
     */
    public function close()
    {
        return $this->update(['closed' => true]);
    }

    /**
     * isClosed.
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * clear.
     */
    public function clear()
    {
        return GlrMessage::where('conversation_id', $this->id)->delete();
    }

    /**
     * unreadMessages.
     */
    public function unreadMessages()
    {
        return GlrMessageNotification::unreadMessagesConversation($this->id)->get();
    }

    /**
     * unreadCount.
     */
    public function unreadCount()
    {
        return $this->unreadMessages()->count();
    }

    /**
     * readAll.
     */
    public function readAll()
    {
        return GlrMessageNotification::unreadMessagesConversation($this->id)
                ->update(['read_at' => now()]);
    }

    /**
     * getMessages.
     */
    public function getMessages()
    {
        return $this->messages->take(25)->get();
    }
}
