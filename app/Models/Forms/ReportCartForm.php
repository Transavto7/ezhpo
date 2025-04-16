<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCartForm extends Model
{
    const DISK = 'report_cart';

    const FILE_EXTENSION = 'DDD';

    protected $primaryKey = 'forms_uuid';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $casts = [
        'attachment' => 'array',
    ];

    protected $fillable = [
        'forms_uuid',
        'attachment',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'forms_uuid', 'uuid');
    }
}
