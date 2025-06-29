<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;

final class PointCondition extends IntCondition
{
    public const TABLE_NAME = 'points';
    public const FIELD_NAME = 'name';

    protected $conditionName = 'point';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        if (isset($rawReminder['point_id'])) {
            return new ClassifierViewModel(
                $rawReminder['point_id'],
                $rawReminder['point_name'],
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return [
            'points.id as point_id',
            DB::raw("concat('[', points.hash_id, '] ', points.name) as point_name"),
        ];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query
            ->leftJoin('points', 'points.id', '=', DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")'));
    }
}
