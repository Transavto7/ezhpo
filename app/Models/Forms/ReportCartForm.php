<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCartForm extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'forms_uuid'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'forms_uuid', 'uuid');
    }
}
