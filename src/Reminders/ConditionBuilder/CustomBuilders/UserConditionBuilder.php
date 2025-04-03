<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder\CustomBuilders;

use App\Point;
use App\User;
use Src\Reminders\Conditions\CityCondition;
use Src\Reminders\Conditions\CompanyCondition;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Conditions\PointCondition;
use Src\Reminders\Conditions\RoleArrayCondition;
use Src\Reminders\Conditions\SubjectCondition;
use Src\Reminders\Conditions\SubjectTypeCondition;
use Src\Reminders\Conditions\UserCondition;

final class UserConditionBuilder implements CustomConditionBuilder
{
    /**
     * @param int|string|array $value
     * @param array<string, int|string|null> $conditionsArray
     * @return Condition[]
     */
    public static function build($value, array $conditionsArray): array
    {
        $userCondition = new UserCondition();
        $roleCondition = new RoleArrayCondition();

        $user = User::query()
            ->select(['id'])
            ->with(['roles'])
            ->where('id', '=', $value)
            ->first();

        $userCondition->setValue($user->id);
        $roleCondition->setValue($user->roles->pluck('id')->toArray());

        return [
            $userCondition->getName() => $userCondition,
            $roleCondition->getName() => $roleCondition,
        ];
    }
}
