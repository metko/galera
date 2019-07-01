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
        //$this->belongsTo(GlrMessage::class);
    }
}
