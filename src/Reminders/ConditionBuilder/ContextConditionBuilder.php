<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder;

use LogicException;
use Src\Reminders\ConditionBuilder\CustomBuilders\CustomConditionBuilder;
use Src\Reminders\ConditionBuilder\CustomBuilders\PointConditionBuilder;
use Src\Reminders\ConditionBuilder\CustomBuilders\SubjectConditionBuilder;
use Src\Reminders\ConditionBuilder\CustomBuilders\UserConditionBuilder;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Conditions\PointCondition;
use Src\Reminders\Conditions\RoleArrayCondition;
use Src\Reminders\Conditions\SubjectCondition;
use Src\Reminders\Conditions\UserCondition;

final class ContextConditionBuilder
{
    /**
     * @var array<class-string<Condition>, class-string<CustomConditionBuilder>>
     */
    protected $customConditionBuilders = [
        PointCondition::class => PointConditionBuilder::class,
        SubjectCondition::class => SubjectConditionBuilder::class,
        UserCondition::class => UserConditionBuilder::class,
    ];

    protected $customConditionMap = [
        'role' => RoleArrayCondition::class,
    ];

    /**
     * @param array<string, int|string|null> $conditionsArray
     * @return array<Condition>
     */
    public function build(array $conditionsArray): array
    {
        $defaultConditions = $this->getDefaultConditions();
        $valueConditions = $this->getValueConditions($conditionsArray);

        return array_values(array_merge($defaultConditions, $valueConditions));
    }

    public function getDefaultConditions(): array
    {
        $conditions = [];
        foreach (AvailableConditions::AVAILABLE_CONDITIONS as $conditionClass) {
            /** @var class-string<Condition> $conditionClass */
            $condition = $conditionClass::create();
            $condition->setValue(null);
            $conditions[$condition->getName()] = $condition;
        }

        return $conditions;
    }

    /**
     * @param array<string, int|string|null> $conditionsArray
     * @return array<string, Condition>
     */
    public function getValueConditions(array $conditionsArray): array
    {
        $conditions = [];
        foreach ($conditionsArray as $name => $value) {
            if (! isset(AvailableConditions::AVAILABLE_CONDITIONS[$name]) && ! isset($this->customConditionMap[$name])) {
                throw new LogicException('Unknown condition: '.$name);
            }

            $conditionClass = AvailableConditions::AVAILABLE_CONDITIONS[$name];

            if (isset($this->customConditionMap[$name])) {
                $conditionClass = $this->customConditionMap[$name];
            }

            $condition = $conditionClass::create();
            $condition->setValue($value ?? null);

            if (isset($this->customConditionBuilders[$conditionClass])) {
                $customConditions = $this->customConditionBuilders[$conditionClass]::build(
                    $value,
                    $conditionsArray
                );

                $conditions = array_merge($conditions, $customConditions);
            } else {
                $conditions[$condition->getName()] = $condition;
            }
        }

        return $conditions;
    }
}
