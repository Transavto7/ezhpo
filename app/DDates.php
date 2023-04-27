<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DDates extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id', 'item_model', 'field', 'days', 'action'
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

    public static function getAll () {
        return self::all();
    }
}
