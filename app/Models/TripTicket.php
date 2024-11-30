<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class TripTicket extends Model
{
    use SoftDeletes;

    const TITLE = 'Реестр путевых листов';

    const SLUG = 'trip_ticket';

    const FIELDS = [
        'ticket_number' => 'Номер ПЛ',
        'created_at' => 'Дата оформления',
        'company_name' => 'Компания',
        'start_date' => 'Дата начала действия',
        'validity_period' => 'Дней действует',
        'medic_form_id' => 'ID медосмотра',
        'driver_name' => 'ФИО водителя',
        'tech_form_id' => 'ID техосмотра',
        'car_number' => 'Госномер Т/С',
        'logistics_method' => 'Вид сообщения',
        'transportation_type' => 'Вид перевозки',
        'template_code' => 'Печатный шаблон',
    ];

    const TABLE_FIELD_KEYS = [
        'ticket_number',
        'created_at',
        'company_name',
        'start_date',
        'validity_period',
        'medic_form_id',
        'driver_name',
        'tech_form_id',
        'car_number',
        'logistics_method',
        'transportation_type',
        'template_code',
    ];

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
}
