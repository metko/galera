<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->id = Str::uuid();
        });
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
}
