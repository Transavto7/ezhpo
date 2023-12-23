<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

final class MisDriver extends Model
{
    protected $table = 'mis_drivers';

    protected $fillable = [
        'driver_id',
        'id_mis'
    ];
}
