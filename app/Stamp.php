<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stamp extends Model
{
    use SoftDeletes;

    public $fillable = [
        'name',
        'company_name',
        'licence',
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
}
