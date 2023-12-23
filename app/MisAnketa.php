<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MisAnketa extends Model
{
    protected $table = 'mis_anketas';

    protected $fillable = [
        'anketa_id',
        'id_mis'
    ];
}
