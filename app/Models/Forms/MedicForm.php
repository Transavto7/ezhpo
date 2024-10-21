<?php

namespace App\Models\Forms;

use App\Enums\FormTypeEnum;
use App\User;
use App\ValueObjects\NotAdmittedReasons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicForm extends Model
{
    protected $primaryKey = 'forms_uuid';

    protected $keyType = 'string';

    public $timestamps = false;

    public $fillable = [
        'forms_uuid',

        'day_hash',

        'type_view',

        'period_pl',
        'is_dop',
        'result_dop',

        'flag_pak',
        'operator_id',
        'terminal_id',

        'driver_group_risk',
        'pressure',
        'tonometer',
        't_people',
        'pulse',
        'proba_alko',
        'alcometer_mode',
        'alcometer_result',
        'test_narko',
        'med_view',
        'admitted',
        'complaint',
        'condition_visible_sliz',
        'condition_koj_pokr',
        'comments',

        'photos',
        'videos',

        'protokol_path',
        'closing_path'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'forms_uuid', 'uuid');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terminal_id', 'id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }

    public function getNotAdmittedReasonsAttribute(): array
    {
        return NotAdmittedReasons::fromForm($this)->getReasons();
    }

    public function scopePakQueueByUser($query, User $user)
    {
        $query
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.type_anketa', FormTypeEnum::PAK_QUEUE);

        if ($user->access('approval_queue_view_all')) {

        } else if ($user->hasRole('head_operator_sdpo')) {
            $query->join('points_to_users', function ($join) use ($user) {
                $join->on('forms.point_id', '=', 'points_to_users.point_id')
                    ->where('points_to_users.user_id', '=', $user->id);
            });
        } else {
            $query->where('forms.user_id', $user->id);
        }
    }
}
