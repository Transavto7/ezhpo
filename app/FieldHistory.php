<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldHistory extends Model
{
    public $fillable = [
        'hash_id', 'user_id', 'value', 'field', 'created_at'
    ];

    public static function getAll () {
        return self::all();
    }
}
