<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder\CustomBuilders;

use Src\Reminders\Conditions\Condition;

interface CustomConditionBuilder
{
    /**
     * @param string|array $value
     * @param array<string, int|string|null> $conditionsArray
     * @return Condition[]
     */
    public static function build($value, array $conditionsArray): array;
}
