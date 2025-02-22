<?php

namespace Src\Employees\Workdays\Eloquent;

use App\Employee;
use App\Point;
use App\Terminal;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property string $uuid
 * @property DateTime $date Время открытия/закрытия смены
 * @property int $timezone Смещение временной зоны относительно UTC (по умолчанию Москва, это +3)
 * @property int|null $open_workday_id ID открытия смены, если это запись закрытия
 * @property int $employee_id ID сотрудника (users)
 * @property int|null $terminal_id ID терминала (users)
 * @property int|null $deleted_id ID сотрудника (users), пользователя, удалившего или восстановившего запись
 * @property int $point_id - ID point
 * @property int|null $t_people Температура сотрудника
 * @property int|null $t_people_test_status Статус теста на температуру (1 - пройден, 0 - нет))
 * @property int|null $pressure_systolic Систолическое давление
 * @property int|null $pressure_diastolic Диастолическое давление
 * @property bool|null $pressure_test_status Статус теста на давление (1 - пройден, 0 - нет)
 * @property int $type_anketa Тип анкеты (int, enum: TypeAnketaSmartEnum)
 * @property int|null $pulse Пульс
 * @property bool|null $pulse_test_status Статус теста на пульс (1 - пройден, 0 - нет)
 * @property float|null $alcometer_result Результат алкотестера
 * @property int|null $alcometer_mode Режим алкотестера
 * @property bool|null $alcometer_test_status Статус теста алкотестера (1 - пройден, 0 - нет)
 * @property bool|null $narko_test_status Тест на наркотики (1 - Пройден, 0 - Нет)
 * @property string|null $photo Фото (ссылка)
 * @property string|null $video Видео (ссылка)
 * @property bool $admitted Есть ли допуск?
 * @property bool $flag_pak Вручную или через терминал: 'Очный' - вручную, 'СДПО А' - через терминал
 * @property bool $is_real Был добавлен в день смены?
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime $deleted_at
 */
class Workday extends Model
{
    use SoftDeletes;

    protected $table = 'workdays';

    public $fillable = [
        'id',
        'uuid',
        'date',
        'timezone',
        'employee_id',
        'terminal_id',
        'open_workday_id',
        'point_id',
        't_people',
        't_people_test_status',
        'pressure_systolic',
        'pressure_diastolic',
        'pressure_test_status',
        'type_anketa',
        'pulse',
        'pulse_test_status',
        'alcometer_result',
        'alcometer_mode',
        'alcometer_test_status',
        'narko_test_status',
        'photo',
        'video',
        'admitted',
        'flag_pak',
        'is_real',
        'created_at',
        'updated_at',
        'deleted_id',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class, 'terminal_id', 'id');
    }

    public function point(): BelongsTo
    {
        return $this->belongsTo(Point::class, 'point_id', 'id');
    }

    public function openWorkday(): BelongsTo
    {
        return $this->belongsTo(self::class, 'open_workday_id', 'id');
    }
}
