<?php

namespace Src\Employees\Workdays\Eloquent;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property int $id
 * @property string $uuid
 * @property DateTime $date Время открытия/закрытия смены
 * @property int $employee_id ID сотрудника (users)
 * @property int $terminal_id ID терминала (users)
 * @property int|null $t_people Температура сотрудника
 * @property int|null $t_people_test_status Статус теста на температуру (1 - пройден, 0 - нет))
 * @property int|null $pressure_systolic Систолическое давление
 * @property int|null $pressure_diastolic Диастолическое давление
 * @property bool|null $pressure_test_status Статус теста на давление (1 - пройден, 0 - нет)
 * @property int $type_anketa Тип анкеты (int, enum: TypeAnketaSmartEnum)
 * @property int|null $pulse Пульс
 * @property bool|null $pulse_test_status Статус теста на пульс (1 - пройден, 0 - нет)
 * @property double|null $alcometer_result Результат алкотестера
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
 */
class Workday extends Model
{

    public $fillable = [
        'id',
        'uuid',
        'date',
        'employee_id',
        'terminal_id',
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
        'updated_at'
        ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }

    public function getEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function getTerminal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
