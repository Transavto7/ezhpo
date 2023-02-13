<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instr extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id',
        'photos',
        'name',
        'descr',
        'type_briefing',
        'youtube',
        'active',
        'sort',
        'deleted_id',
        'is_default'
    ];

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }
}
