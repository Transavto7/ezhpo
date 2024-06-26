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
        'discount',
        'deleted_id'
    ];

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete();
    }

    public function getDiscount($total) {
        $is_discount_valid = false;
        eval('$is_discount_valid = ' . $total . $this->trigger . $this->porog . ';');

        if ($is_discount_valid) {
            return $this->discount;
        }

        return false;
    }
}
