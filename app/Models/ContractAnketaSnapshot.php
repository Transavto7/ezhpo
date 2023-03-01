<?php

namespace App\Models;

use App\Company;
use App\Driver;
use Illuminate\Database\Eloquent\Model;

// archive
/**
 * App\Models\ContractAnketaSnapshot
 *
 * @property int $id
 * @property int|null $anketa_id
 * @property int|null $contract_id
 * @property int|null $time_of_action
 * @property string|null $sum
 * @property int|null $company_id
 * @property int|null $our_company_id
 * @property int|null $driver_id
 * @property int|null $car_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $car
 * @property-read int|null $car_count
 * @property-read Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $driver
 * @property-read int|null $driver_count
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereAnketaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereCarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereOurCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereTimeOfAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractAnketaSnapshot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContractAnketaSnapshot extends Model
{
    protected $table = 'contract_anketa_snapshot';

    protected $casts
        = [
            'company_snapshot' => 'json',   // Инфа на момент создания анкеты
            'driver_snapshot'  => 'json',   // Инфа на момент создания анкеты
            'car_snapshot'     => 'json',   // Инфа на момент создания анкеты
        ];

    protected $guarded = [];


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
