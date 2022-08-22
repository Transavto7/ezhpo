<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id', 'name',
        'note', 'user_id', 'req_id',
        'pv_id', 'town_id', 'products_id', 'where_call', 'where_call_name', 'inn',
        'procedure_pv',
        'dismissed',
        'has_actived_prev_month',
        'document_bdd'
    ];

    public function cars()
    {
        return $this->hasMany(Car::class, 'company_id', 'id');
    }

    public static function getAll () {
        $user = auth()->user();

        if($user->hasRole('client')) {
            $c_id = User::getUserCompanyId('id');

            if($c_id) {
                return self::find($c_id)->get();
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
