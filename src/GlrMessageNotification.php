<?php

namespace Metko\Galera;

use Illuminate\Database\Eloquent\Model;

class GlrMessageNotification extends Model
{
    protected $guarded = [];
    protected $visible = ['read_at'];

    public $incrementing = false;

    public static function boot()
    {
        parent::boot();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('galera.table_prefix').'message_notifications';
    }

    public function message()
    {
        return $this->belongsTo(GlrMessage::class);
    }

    public function conversation()
    {
        return $this->belongsTo(GlrConversation::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(config('galera.user_class', 'from_user_id'));
    }

    public function toUser()
    {
        return $this->belongsTo(config('galera.user_class', 'to_user_id'));
    }

    /**
     * scopeUnreadMessagesConversation.
     *
     * @param mixed $query
     * @param mixed $conversation_id
     */
    public function scopeUnreadMessagesConversation($query, $conversation_id)
    {
        $query->where('read_at', null)->where('conversation_id', $conversation_id);
    }

    /**
     * scopeUnreadMessagesUser.
     *
     * @param mixed $query
     * @param mixed $user_id
     */
    public function scopeUnreadMessagesUser($query, $user_id)
    {
        $query->where('read_at', null)->where('to_user_id', $user_id);
    }
}
