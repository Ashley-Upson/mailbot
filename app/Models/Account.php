<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $table = 'accounts';

    public $fillable = [
        'name',
        'active',
        'email',
        'password',
        'host',
        'port',
        'encryption'
    ];

    public $timestamps = [
        'created_at',
        'updated_at'
    ];
}
