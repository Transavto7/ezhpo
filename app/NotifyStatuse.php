<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifyStatuse extends Model
{
    public $fillable = [
        'notify_id', 'status', 'user_id'
    ];
}
