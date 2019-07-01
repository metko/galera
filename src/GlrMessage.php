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
    protected $touch = [
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

    public function isResponse()
    {
        return  !empty($this->reffer_to);
    }

    public function reffer()
    {
        return $this->hasOne(GlrMessage::class, 'id', 'reffer_to');
    }

    public function owner()
    {
        return $this->belongsTo(config('galera.user_class'));
    }

    public function conversation()
    {
        return $this->belongsTo(GlrConversation::class);
    }

    public function isRead()
    {
        foreach ($this->status as $status) {
            if (!$status->read_at) {
                return false;
            }
        }

        return true;
    }

    public function status()
    {
        // Le message X sur la table notification est-il lu pour les utilisateur de la conversation
        return $this->hasMany(GlrMessageNotification::class, 'message_id');
    }

    public function markAsRead()
    {
        // Le message X sur la table notification est-il lu pour les utilisateur de la conversation
        return GlrMessageNotification::where('message_id', $this->id)
          ->update(['read_at' => now()]);
    }
}
