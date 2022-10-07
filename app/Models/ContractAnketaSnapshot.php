<?php

namespace App\Models;

use App\Company;
use App\Driver;
use Illuminate\Database\Eloquent\Model;

class ContractAnketaSnapshot extends Model
{
    protected $table = 'contract_anketa_snapshot';

    protected $casts
        = [
            'company_snapshot' => 'json',   // Инфа на момент создания анкеты
            'driver_snapshot'  => 'json',   // Инфа на момент создания анкеты
            'car_snapshot'     => 'json',   // Инфа на момент создания анкеты
        ];


    public function company()
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
            'id'
        );
    }

    public function driver()
    {
        return $this->hasMany(
            Driver::class,
            'driver_id',
            'id'
        );
    }

    public function car()
    {
        return $this->hasMany(
            Driver::class,
            'car_id',
            'id'
        );
    }


}
