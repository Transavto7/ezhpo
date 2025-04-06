<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Actions\SaveHolidays;

use Src\Employees\Holidays\Eloquent\Holiday;

final class SaveHolidaysHandler
{
    public function handle(SaveHolidaysAction $action)
    {
        if (count($action->getDeleteHolidays()) > 0) {
            Holiday::query()->whereIn('date', $action->getDeleteHolidays())->delete();
        }

        if (count($action->getNewHolidays()) > 0) {
            $dates = array_map(function ($holiday) {
                return ['date' => $holiday];
            }, $action->getNewHolidays());

            Holiday::query()->insert($dates);
        }
    }
}
