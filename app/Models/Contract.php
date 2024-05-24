<?php

namespace App\Models;

use App\Car;
use App\Company;
use App\Driver;
use App\Product;
use App\Req;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    public static $types = [
        1 => 'Абонентская плата',
        2 => 'Разовая',
    ];

    protected $table = 'contracts';

    protected $appends = ['name_with_dates'];

    protected $casts = [
        'date_of_end' => 'datetime',
        'date_of_start' => 'datetime'
    ];

    protected $guarded = [];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'contract_service',
            'contract_id',
            'service_id',
            'id',
            'id'
        )->withPivot('service_cost');
    }

    public function getNameWithDatesAttribute()
    {
        return $this->name
            . " \nс: " .
            ($this->date_of_start ? $this->date_of_start->format('d-m-Y') : '')
            . " \nпо: " .
            ($this->date_of_end ? $this->date_of_end->format('d-m-Y') : '');
    }

    public function scopeMain($query)
    {
        return $query->where("main_for_company", 1);
    }

    /**
     * Для определения текущего договора
     *
     * @param Builder $query
     * @param Carbon $date
     *
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeForDate(Builder $query, Carbon $date)
    {
        return $query
            ->whereDate("date_of_end", '>=', $date)
            ->whereDate("date_of_start", '<=', $date);
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(
            Driver::class,
            'driver_contact_pivot',
            'contract_id',
            'driver_id'
        );
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(
            Car::class,
            'car_contact_pivot',
            'contract_id',
            'car_id'
        );
    }

    public function deleted_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
            'id'
        )->withDefault();
    }

    public function our_company(): BelongsTo
    {
        return $this->belongsTo(
            Req::class,
            'our_company_id',
            'id'
        )->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }
}
