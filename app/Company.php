<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Company
 *
 * @property int $id
 * @property string $hash_id
 * @property string $req_id
 * @property string|null $user_id
 * @property string|null $pv_id
 * @property string|null $town_id
 * @property int|null $inn
 * @property string|null $products_id
 * @property string|null $where_call
 * @property string $name
 * @property string|null $payment_form
 * @property string $procedure_pv
 * @property string|null $note
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $dismissed
 * @property string|null $where_call_name
 * @property string|null $has_actived_prev_month
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $bitrix_link
 * @property string|null $document_bdd
 * @property string|null $deleted_id
 * @property int|null $pressure_systolic
 * @property int|null $pressure_diastolic
 * @property int $required_type_briefing
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Car[] $cars
 * @property-read int|null $cars_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Contract[] $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\User|null $deleted_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_bdd
 * @property-read int|null $inspections_bdd_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_medic
 * @property-read int|null $inspections_medic_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_pechat_pl
 * @property-read int|null $inspections_pechat_pl_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_report_cart
 * @property-read int|null $inspections_report_cart_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Anketa[] $inspections_tech
 * @property-read int|null $inspections_tech_count
 * @property-read \App\Point|null $point
 * @property-read \App\User|null $responsible
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Query\Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereBitrixLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDismissed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDocumentBdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereHasActivedPrevMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePaymentForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePressureDiastolic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePressureSystolic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereProcedurePv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereProductsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereReqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereRequiredTypeBriefing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereTownId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereWhereCall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereWhereCallName($value)
 * @method static \Illuminate\Database\Query\Builder|Company withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Company withoutTrashed()
 * @mixin \Eloquent
 */
class Company extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id', 'name',
        'note', 'comment', 'procedure_pv',
        'user_id', 'req_id',
        'pv_id', 'town_id', 'products_id', 'where_call', 'where_call_name', 'inn',
        'required_type_briefing',
        'dismissed',
        'has_actived_prev_month',
        'bitrix_link',
        'document_bdd',
        'deleted_id',
        'pressure_systolic',
        'pressure_diastolic',
        'time_of_alcohol_ban',
        'time_of_pressure_ban'
    ];


    public function point()
    {
        return $this->belongsTo(Point::class, 'pv_id')
            ->withDefault();
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function deleted_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function contracts()
    {
        return $this->hasMany(
            Contract::class,
            'company_id',
            'id'
        );
    }

    public function inspections_tech() : HasMany
    {
        return $this->hasMany(Anketa::class, 'company_id', 'id')
                    ->where('type_anketa', 'tech');
    }
    public function inspections_medic()
    {
        return $this->hasMany(Anketa::class, 'company_id', 'id')
                    ->where('type_anketa', 'medic');
    }
    public function inspections_pechat_pl()
    {
        return $this->hasMany(Anketa::class, 'company_id', 'id')
                    ->where('type_anketa', 'pechat_pl');
    }
    public function inspections_bdd()
    {
        return $this->hasMany(Anketa::class, 'company_id', 'id')
                    ->where('type_anketa', 'bdd');
    }
    public function inspections_report_cart()
    {
        return $this->hasMany(Anketa::class, 'company_id', 'id')
                    ->where('type_anketa', 'report_cart');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'company_id', 'id');
    }
    public function drivers()
    {
        return $this->hasMany(Driver::class, 'company_id', 'id');
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete(); // TODO: Change the autogenerated stub
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
