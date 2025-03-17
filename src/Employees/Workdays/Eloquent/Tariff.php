<?php

namespace Src\Employees\Workdays\Eloquent;

use App\Point;
use App\Role;
use App\Town;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name Название тарифа
 * @property DateTime $date_from Дата и время начала действия тарифа
 * @property DateTime $date_to Дата и время завершения действия тарифа
 * @property int|null $town_id ID города
 * @property int|null $point_id ID point
 * @property int $role_id ID роли, для которой действует тариф
 * @property int $price_cfg Коэффициент выходного (прибавляется к стоимости часа, если это выходной день)
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Tariff extends Model
{
    protected $table = 'tariffs';

    public $fillable = [
        'id',
        'name',
        'date_from',
        'date_to',
        'town_id',
        'point_id',
        'role_id',
        'price_cfg',
        'created_at',
        'updated_at',
    ];

    public function town(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'town_id', 'id');
    }

    public function pv(): BelongsTo
    {
        return $this->belongsTo(Point::class, 'point_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
