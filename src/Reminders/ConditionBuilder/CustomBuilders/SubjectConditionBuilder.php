<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder\CustomBuilders;

use LogicException;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Conditions\SubjectCondition;
use Src\Reminders\Conditions\SubjectTypeCondition;

final class SubjectConditionBuilder implements CustomConditionBuilder
{
    /**
     * @param int|string|array $value
     * @param array<string, int|string|null> $conditionsArray
     * @return Condition[]
     * @throws \Exception
     */
    public static function build($value, array $conditionsArray): array
    {
        $subjectCondition = new SubjectCondition();
        $subjectTypeCondition = new SubjectTypeCondition();

        if (! isset($conditionsArray[$subjectTypeCondition->getName()])) {
            throw new LogicException('Use subject condition without subject type!');
        }

        $subjectTypeCondition->setValue($conditionsArray[$subjectTypeCondition->getName()]);
        $subjectCondition->setValue($value);

        return [
            $subjectCondition->getName() => $subjectCondition,
            $subjectTypeCondition->getName() => $subjectTypeCondition,
        ];
    }
}
