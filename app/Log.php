<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ramsey\Uuid\Uuid;

class Log extends Model
{
    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'data',
        'type',
        'model_id',
        'model_type',
        'uuid'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeDateFrom($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('logs.created_at', '>=', Carbon::parse($value));
        });
    }

    public function scopeDateTo($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('logs.created_at', '<=', Carbon::parse($value));
        });
    }

    public function scopeModelTypes($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('logs.model_type', $values);
        });
    }

    public function scopeModelId($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->where('logs.model_id', 'like', "%$value%");
        });
    }

    public function scopeUuid($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->where('logs.uuid', 'like', "%$value%");
        });
    }

    public function scopeUserIds($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('logs.user_id', $values);
        });
    }

    public function scopeActionTypes($query, $values)
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('logs.type', $values);
        });
    }
}
