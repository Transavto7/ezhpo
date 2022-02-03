<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Req extends Model
{
    public $fillable = [
        'hash_id', 'name', 'inn', 'bik', 'kc', 'rc', 'banks', 'director', 'director_fio',
        'signature', 'seal'
    ];

    public function getName ($id)
    {
        $reqs = Req::find($id);

        if(!$reqs) $reqs = '';
        else $reqs = $reqs['name'];

        return $reqs;
    }

    public static function getAll () {
        return self::all();
    }
}
