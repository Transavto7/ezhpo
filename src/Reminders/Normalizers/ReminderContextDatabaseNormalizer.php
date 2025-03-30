<?php

declare(strict_types=1);

namespace Src\Reminders\Normalizers;

use Src\Reminders\ConditionBuilder\AvailableConditions;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Entities\Reminder;

final class ReminderContextDatabaseNormalizer
{
    /**
     * @param array<Condition> $context
     * @return array<string, int|string|null>
     */
    public function normalize(array $context): array
    {
        $contextArray = [];

        foreach ($context as $condition) {
            $contextArray[$condition->getName()] = $condition->getValue();
        }

        return $contextArray;
    }

    /**
     * @param array<string, int|string|null> $contextArray
     * @return array<Condition>
     */
    public function denormalize(array $contextArray): array
    {
        $context = [];

        foreach (AvailableConditions::AVAILABLE_CONDITIONS as $conditionClass) {
            /** @var class-string<Condition> $conditionClass */
            $condition = $conditionClass::create();

            $condition->setValue($contextArray[$condition->getName()] ?? null);
            $context[] = $condition;
        }

        return $context;
    }
}
