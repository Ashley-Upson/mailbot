<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $table = 'applications';

    public $fillable = [
        'name',
        'app_key',
        'account_id',
        'process',
        'slack_url',
        'default_priority'
    ];

    public $timestamps = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
