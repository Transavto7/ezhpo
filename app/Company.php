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
        $user = auth()->user();

        if($user->hasRole('client', '==')) {
            $c_id = User::getUserCompanyId('id');

            if($c_id) {
                return self::where('id', $c_id)->get();
            }
        }

        return self::all();
    }

    public function getName ($id, $field = 'id')
    {
        $company = Company::where($field, $id)->first();

        if(!$company) $company = '';
        else $company = $company['name'];

        return $company;
    }
}
