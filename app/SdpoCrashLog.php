<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SdpoCrashLog extends Model
{
    protected $fillable = [
        'uuid',
        'version',
        'terminal_id',
        'point_id',
        'version',
        'type',
        'happened_at'
    ];

    public function scopeCreatedAtFrom($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('sdpo_crash_logs.created_at', '>=', Carbon::parse($value));
        });
    }

    public function scopeCreatedAtTo($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('sdpo_crash_logs.created_at', '<=', Carbon::parse($value));
        });
    }

    public function scopeHappenedAtFrom($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('sdpo_crash_logs.happened_at', '>=', Carbon::parse($value));
        });
    }

    public function scopeHappenedAtTo($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->whereDate('sdpo_crash_logs.happened_at', '<=', Carbon::parse($value));
        });
    }

    public function scopeTypes($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('sdpo_crash_logs.type', $values);
        });
    }

    public function scopeVersions($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('sdpo_crash_logs.version', $values);
        });
    }

    public function scopePoints($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('sdpo_crash_logs.point_id', $values);
        });
    }

    public function scopeTerminals($query, $values = [])
    {
        return $query->when(count($values), function ($subQuery) use ($values) {
            return $subQuery->whereIn('sdpo_crash_logs.terminal_id', $values);
        });
    }

    public function scopeUuid($query, $value)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            return $subQuery->where('sdpo_crash_logs.uuid', 'like', "%$value%");
        });
    }
}
