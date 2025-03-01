<?php

namespace App\Models;

use Carbon\Carbon;
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
        'model_type',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function scopeDateFrom($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('form_events.created_at', '>=', Carbon::parse($value));
        });
    }

    public function scopeDateTo($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('form_events.created_at', '<=', Carbon::parse($value));
        });
    }

    public function scopeModelTypes($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('form_events.model_type', $values);
        });
    }

    public function scopeModelId($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->where('forms.id', 'like', "%$value%");
        });
    }

    public function scopeUuid($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->where('form_events.uuid', 'like', "%$value%");
        });
    }

    public function scopeUserIds($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('form_events.user_id', $values);
        });
    }

    public function scopeActionTypes($query, $values)
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('form_events.event_type', $values);
        });
    }
}
