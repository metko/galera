<?php

namespace Metko\Galera;

use Tests\Models\User;
use Illuminate\Database\Eloquent\Model;

class GlrMessage extends Model
{
    protected $guarded = [];

    public function isResponse()
    {
        return  !empty($this->reffer_to);
    }

    public function reffer()
    {
        return $this->hasOne('Metko\Galera\GlrMessage', 'id', 'reffer_to');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(GlrConversation::class);
    }
}
