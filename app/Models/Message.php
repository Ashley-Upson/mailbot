<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $table = 'messages';

    public $fillable = [
        'application_id',
        'to',
        'priority',
        'subject',
        'body',
        'status',
        'sent_at'
    ];

    public $timestamps = [
        'created_at',
        'updated_at'
    ];

    public function application()
    {
        return $this->hasOne(Application::class, 'id', 'application_id');
    }
}
