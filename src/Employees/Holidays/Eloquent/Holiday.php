<?php

namespace Src\Employees\Holidays\Eloquent;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property DateTime $date Дата, только до дня, когда применяется логика выходного
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Holiday extends Model
{
    protected $table = 'holidays';

    public $fillable = [
        'id',
        'date',
        'created_at',
        'updated_at',
    ];
}
