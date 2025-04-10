<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;

final class RoleCondition extends IntCondition
{
    public const TABLE_NAME = 'roles';
    public const FIELD_NAME = 'guard_name';

    protected $conditionName = 'role';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        if (isset($rawReminder['role_id'])) {
            return new ClassifierViewModel(
                $rawReminder['role_id'],
                $rawReminder['role_name'],
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return ['roles.id as role_id', 'roles.guard_name as role_name'];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query
            ->leftJoin('roles', 'roles.id', '=', DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")'));
    }
}
