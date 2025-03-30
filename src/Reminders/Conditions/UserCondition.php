<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;

final class UserCondition extends IntCondition
{
    protected $conditionName = 'user';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        if (isset($rawReminder['user_id'])) {
            return new ClassifierViewModel(
                $rawReminder['user_id'],
                $rawReminder['user_name'],
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return ['users.id as user_id', DB::raw("concat(users.name, ' (', users.login , ')') as user_name")];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query
            ->leftJoin('users', 'users.id', '=', DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")'));
    }
}
