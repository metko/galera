<?php

namespace Metko\Galera;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlrMessage extends Model
{
    use SoftDeletes;

    protected $guarded = [];

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
}
