<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $guarded = [];


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
}
