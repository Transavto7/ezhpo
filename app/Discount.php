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


    public function add($total, $price) {
        $is_discount_valid = false;
        eval('$is_discount_valid = ' . $total . $this->trigger . $this->porog . ';');

        if($is_discount_valid) {
            $disc = ($price * $this->discount) / 100;

            return $price - $disc;
        }
    }
}
