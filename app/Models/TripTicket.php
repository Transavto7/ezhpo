<?php

namespace App\Models;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class TripTicket extends Model
{
    use SoftDeletes;

    const SLUG = 'trip_ticket';

    const FILTERS = [
        'ticket_number' => 'Номер ПЛ',
        'created_at' => 'Дата оформления',
        'company_id' => 'Компания',
        'start_date' => 'Дата начала действия',
        'validity_period' => 'Дней действует',
        'medic_form_id' => 'ID медосмотра',
        'driver_id' => 'ФИО водителя',
        'tech_form_id' => 'ID техосмотра',
        'car_id' => 'Госномер Т/С',
        'logistics_method' => 'Вид сообщения',
        'transportation_type' => 'Вид перевозки',
        'template_code' => 'Печатный шаблон',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }

    protected $fillable = [
        'ticket_number',
        'company_id',
        'start_date',
        'validity_period',
        'medic_form_id',
        'driver_id',
        'tech_form_id',
        'car_id',
        'logistics_method',
        'transportation_type',
        'template_code',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'hash_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'hash_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'hash_id');
    }

    public function medicForm(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'medic_form_id', 'id');
    }

    public function techForm(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'tech_form_id', 'id');
    }
}
