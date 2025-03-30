<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder;

use Src\Reminders\Conditions\CityCondition;
use Src\Reminders\Conditions\CompanyCondition;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Conditions\PointCondition;
use Src\Reminders\Conditions\RoleCondition;
use Src\Reminders\Conditions\SubjectCondition;
use Src\Reminders\Conditions\SubjectTypeCondition;
use Src\Reminders\Conditions\UserCondition;

final class AvailableConditions
{
    /** @var class-string<Condition>[] */
    public const AVAILABLE_CONDITIONS = [
        UserCondition::class,
        CityCondition::class,
        CompanyCondition::class,
        PointCondition::class,
        RoleCondition::class,
        SubjectCondition::class,
        SubjectTypeCondition::class,
    ];
}
