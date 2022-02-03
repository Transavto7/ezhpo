<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $fillable = [
        'hash_id', 'name',
        'note', 'user_id', 'req_id',
        'pv_id', 'town_id', 'products_id', 'where_call', 'inn', 'payment_form', 'procedure_pv',
        'dismissed'
    ];

    public static function getAll () {
        return self::all();
    }

    public function getName ($id)
    {
        $company = Company::find($id);

        if(!$company) $company = '';
        else $company = $company['name'];

        return $company;
    }
}
