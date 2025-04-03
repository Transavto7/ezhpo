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
        'user' => UserCondition::class,
        'city' => CityCondition::class,
        'company' => CompanyCondition::class,
        'point' => PointCondition::class,
        'role' => RoleCondition::class,
        'subject' => SubjectCondition::class,
        'subject_type' => SubjectTypeCondition::class,
    ];
}
