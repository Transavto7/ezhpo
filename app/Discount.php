<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $fillable = [
        'hash_id',
        'products_id',
        'trigger',
        'porog',
        'discount'
    ];
}
