<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instr extends Model
{
    public $fillable = [
        'hash_id',
        'photos',
        'name',
        'descr',
        'type_briefing',
        'youtube',
        'active'
    ];
}
