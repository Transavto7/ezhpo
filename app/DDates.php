<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DDates extends Model
{
    public $fillable = [
        'hash_id', 'item_model', 'field', 'days', 'action'
    ];

    public static function getAll () {
        return self::all();
    }
}
