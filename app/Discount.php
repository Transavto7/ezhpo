<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id',
        'products_id',
        'trigger',
        'porog',
        'discount'
    ];


    public function getDiscount($total) {
        $is_discount_valid = false;
        eval('$is_discount_valid = ' . $total . $this->trigger . $this->porog . ';');

        if ($is_discount_valid) {
            return $this->discount;
        }

        return false;
    }
}
