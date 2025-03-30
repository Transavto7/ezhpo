<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;

final class CityCondition extends IntCondition
{
    protected $conditionName = 'city';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        if (isset($rawReminder['city_id'])) {
            return new ClassifierViewModel(
                $rawReminder['city_id'],
                $rawReminder['city_name'],
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return ['towns.id as city_id', 'towns.name as city_name'];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query
            ->leftJoin('towns', 'towns.id', '=', DB::raw('JSON_EXTRACT(context, "$.city")'));
    }
}
