<?php

declare(strict_types=1);

namespace Src\Reminders\ConditionBuilder\CustomBuilders;

use App\Point;
use Src\Reminders\Conditions\CityCondition;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Conditions\PointCondition;

final class PointConditionBuilder implements CustomConditionBuilder
{
    /**
     * @param int|string|array $value
     * @param array<string, int|string|null> $conditionsArray
     * @return Condition[]
     */
    public static function build($value, array $conditionsArray): array
    {
        $pointCondition = new PointCondition();

        if ($value === null) {
            $pointCondition->setValue(null);

            return [
                $pointCondition->getName() => $pointCondition,
            ];
        }

        $pointCondition->setValue($value);
        $cityCondition = new CityCondition();
        $cityCondition->setValue(null);

        $cityId = optional(Point::query()->where('id', '=', $pointCondition->getValue())->select('pv_id')->first())->pv_id;

        if ($cityId !== null) {
            $cityCondition->setValue($cityId);
        }

        return [
            $pointCondition->getName() => $pointCondition,
            $cityCondition->getName() => $cityCondition,
        ];
    }
}
