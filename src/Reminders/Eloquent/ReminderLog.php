<?php

namespace Src\Reminders\Eloquent;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $action Действие
 * @property int $reminder_id ID напоминания
 * @property array $payload Параметры напоминания (JSON)
 * @property int|null $user_id ID сотрудника
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class ReminderLog extends Model
{
    use SoftDeletes;

    protected $table = 'reminder_logs';

    public $fillable = [
        'id',
        'action',
        'reminder_id',
        'payload',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
