<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class FormEvent extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }

    protected $fillable = [
        'form_uuid',
        'uuid',
        'event_type',
        'payload',
        'user_id',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
