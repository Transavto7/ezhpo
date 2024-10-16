<?php

namespace App\Models\Forms;

use App\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechForm extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'forms_uuid',

        'day_hash',

        'type_view',

        'is_dop',
        'result_dop',

        'car_id',

        'odometer',
        'number_list_road',
        'point_reys_control'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'forms_uuid', 'uuid');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'hash_id');
    }
}
