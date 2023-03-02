<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\Car
 *
 * @property int $id
 * @property int|null $old_id
 * @property string $hash_id
 * @property int $company_id
 * @property string $gos_number
 * @property string $mark_model
 * @property string $type_auto
 * @property string|null $products_id
 * @property string|null $trailer
 * @property string|null $date_osago
 * @property string|null $date_prto
 * @property string|null $date_techview
 * @property string|null $time_skzi
 * @property string|null $payment_form
 * @property string|null $count_pl
 * @property string|null $note
 * @property string $procedure_pv
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $town_id
 * @property string $dismissed
 * @property string $autosync_fields
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $deleted_id
 * @property int|null $contract_id
 * @property-read \App\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|Contract[] $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\User|null $deleted_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_tech
 * @property-read int|null $inspections_tech_count
 * @method static \Illuminate\Database\Eloquent\Builder|Car newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Car newQuery()
 * @method static Builder|Car onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Car query()
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereAutosyncFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereCountPl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDateOsago($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDatePrto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDateTechview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereDismissed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereGosNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereMarkModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereOldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car wherePaymentForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereProcedurePv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereProductsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTimeSkzi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTownId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTrailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTypeAuto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereUpdatedAt($value)
 * @method static Builder|Car withTrashed()
 * @method static Builder|Car withoutTrashed()
 * @mixin \Eloquent
 */
class Car extends Model
{
    use SoftDeletes;

    public $fillable
        = [
            'hash_id',
            //'old_id',
            'gos_number',
            'mark_model',
            'type_auto',
            'products_id',
            'trailer',
            'company_id',
            'count_pl',
            'note',
            'procedure_pv',
            'date_prto',
            'date_techview',
            'time_skzi',
            'date_osago',
            'town_id',
            'dismissed',
            'autosync_fields',
            'contract_id',
            'deleted_id',
        ];

    public function inspections_tech()
    {
        return $this->hasMany(
            Anketa::class,
            'car_id',
            'hash_id'
        )
                    ->where('type_anketa', 'tech');
    }


    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

//    public function contracts()
//    {
//        return $this->hasMany(
//            Contract::class,
//            'company_id',
//            'id'
//        );
//    }

    public function contracts()
    {
        return $this->belongsToMany(
            Contract::class,
            'car_contact_pivot',
            'car_id',
            'contract_id'
        );
    }

//    public function contract()
//    {
//        return $this->belongsTo(Contract::class, 'contract_id', 'id')
//                    ->withDefault();
//    }
    // sorry for name
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }

    public function getAutoSyncFieldsFromHashId($hash_id)
    {
        $element = self::where('hash_id', $hash_id)->first();

        if ($element) {
            if ($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

    public static function getAutoSyncFields($hash_id)
    {
        $element = self::where('hash_id', $hash_id)->first();

        if ($element) {
            if ($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

   /* public static function calcServices($hash_id, $type_anketa = '', $type_view = '', $count = 0)
    {
        $element = self::where('hash_id', $hash_id)->first();
        $data    = '';

        if ($element) {
            if (isset($element->products_id)) {
                $data     = [];
                $services = explode(',', $element->products_id);

                $services = Product::whereIn('id', $services)
                                   ->where('type_anketa', $type_anketa)
                                   ->where('type_view', 'LIKE', "%$type_view%")->get();

                foreach ($services as $serviceKey => $service) {
                    $discounts = Discount::where('products_id', $service->id)->get();

                    if ($discounts->count()) {
                        foreach ($discounts as $discount) {
                            eval('$is_discount_valid = '.$count.$discount->trigger.$discount->porog.';');

                            if ($is_discount_valid) {
                                $disc   = ($service->price_unit * $discount->discount) / 100;
                                $p_unit = $service->price_unit;

                                $services[$serviceKey]->price_unit = $p_unit - $disc;
                            }
                        }
                    }
                }

                if (count($services)) {
                    $data['summ'] = $services->sum('price_unit').'₽';
                } else {
                    if ( !isset($element->products_id)) {
                        $data['summ'] = "<span class='text-red'>Услуги не указаны</span>";
                    } else {
                        $data['summ'] = "<span class='text-red'>Услуги не найдены</span>";
                    }
                }

                return "<div><b>$data[summ]</b></div>";
            } else {
                $data = 'Услуги не указаны';
            }
        }

        return $data;
    }*/

    public static function getAll()
    {
        return self::all();
    }

    // Получение пункта выпуска
    public static function getName($id = 0)
    {
        $car = Car::where('hash_id', $id)->first();

        if ($car) {
            $car = $car->gos_number;
        } else {
            $car = '';
        }

        return $car;
    }
}
