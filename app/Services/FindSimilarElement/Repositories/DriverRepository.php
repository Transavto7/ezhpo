<?php

namespace App\Services\FindSimilarElement\Repositories;

use App\Driver;
use Illuminate\Database\Eloquent\Builder;

final class DriverRepository
{
    public function findByName(string $name, string $companyId, $excludeId = null, bool $withTrashed = false): ?Driver
    {
        return Driver::query()
            ->when($withTrashed, function (Builder $query) {
                $query->withTrashed();
            })
            ->when($excludeId, function ($builder) use ($excludeId) {
                $builder->where('id', '<>', $excludeId);
            })
            ->where('fio', '=', $name)
            ->where('company_id', '=', $companyId)
            ->orderBy('deleted_at')
            ->first();
    }
}
