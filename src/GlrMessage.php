<?php

namespace Metko\Galera;

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
}
