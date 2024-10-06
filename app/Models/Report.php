<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Report extends Model
{
    protected $fillable = [
        'uuid',
        'type',
        'status',
        'user_id',
        'payload',
        'error',
        'path',
    ];

    protected $casts = [
        'payload' => 'array',
        'error' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }
}
