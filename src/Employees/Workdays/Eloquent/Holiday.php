<?php

namespace Src\Employees\Workdays\Eloquent;

use App\Point;
use App\Role;
use App\Town;
use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property DateTime $date Дата, только до дня, когда применяется логика выходного
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Holiday extends Model
{
    protected $table = 'workdays';

    public $fillable = [
        'id',
        'date',
        'created_at',
        'updated_at',
    ];
}
