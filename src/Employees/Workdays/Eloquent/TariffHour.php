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
 * @property int $tariff_id ID тарифа
 * @property int $hour Час (с 0 до 23)
 * @property int $price Стоимость часа
 */
class TariffHour extends Model
{
    protected $table = 'tariff_hours';

    public $fillable = [
        'id',
        'tariff_id',
        'hour',
        'price'
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }
}
