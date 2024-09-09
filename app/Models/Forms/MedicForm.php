<?php

namespace App\Models\Forms;

use App\User;
use App\ValueObjects\NotAdmittedReasons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicForm extends Model
{
    public $fillable = [
        'form_uuid',

        'type_view',

        'is_dop',
        'result_dop',
        'realy',

        'flag_pak',
        'operator_id',

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

        'photos',
        'videos',

        'protokol_path',
        'closing_path',
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
}
