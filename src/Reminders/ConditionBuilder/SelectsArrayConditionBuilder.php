<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder;

use InvalidArgumentException;
use Src\Reminders\Conditions\Condition;

final class SelectsArrayConditionBuilder
{
    /**
     * @param array<string, array{id: string|int, name: string}|null> $conditionsArray
     * @return array<Condition>
     */
    public function build(array $conditionsArray): array
    {
        $conditions = [];
        foreach (AvailableConditions::AVAILABLE_CONDITIONS as $conditionClass) {
            /** @var class-string<Condition> $conditionClass */
            $condition = $conditionClass::create();

            if (isset($conditionsArray[$condition->getName()]) && ! is_array($conditionsArray[$condition->getName()])) {
                throw new InvalidArgumentException('Invalid condition: '.$condition->getName());
            }

            $condition->setValue($conditionsArray[$condition->getName()]['id'] ?? null);
            $conditions[] = $condition;
        }

        return $conditions;
    }
}
