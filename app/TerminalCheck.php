<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerminalCheck extends Model
{
    protected $fillable = [
        'user_id',
        'serial_number',
        'date_check',
    ];

    protected $casts = [
        'date_check' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
