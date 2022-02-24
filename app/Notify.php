<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    public $fillable = [
        'user_id', 'message', 'status'
    ];
}
