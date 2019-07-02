<?php

namespace Metko\Galera;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlrMessage extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public $incrementing = false;

    public $casts = [
        'id' => 'string',
    ];
    protected $touches = [
        'conversation',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('galera.table_prefix').'messages';
    }

    /**
     * reffer.
     */
    public function reffer()
    {
        return $this->hasOne(GlrMessage::class, 'id', 'reffer_to');
    }

    /**
     * owner.
     */
    public function owner()
    {
        return $this->belongsTo(config('galera.user_class'));
    }

    /**
     * conversation.
     */
    public function conversation()
    {
        return $this->belongsTo(GlrConversation::class);
    }

    /**
     * status.
     */
    public function status()
    {
        return $this->hasMany(GlrMessageNotification::class, 'message_id');
    }

    /**
     * isResponse.
     */
    public function isResponse()
    {
        return  !empty($this->reffer_to);
    }

    /**
     * isRead.
     */
    public function isRead()
    {
        foreach ($this->status as $status) {
            if (!$status->read_at) {
                return false;
            }
        }

        return true;
    }

    /**
     * markAsRead.
     */
    public function markAsRead()
    {
        return GlrMessageNotification::where('message_id', $this->id)
          ->update(['read_at' => now()]);
    }

    /**
     * markAsRead.
     */
    public function scopeOfConversation($query, $conversationID)
    {
        $query->where('conversation_id', $conversationID)
          ->orderBy('created_at', 'desc');
    }
}
