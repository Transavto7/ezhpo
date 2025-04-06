<?php

declare(strict_types=1);

namespace Src\Reminders\Factories;

use App\Car;
use App\Driver;
use Illuminate\Database\Eloquent\Builder;
use Src\Reminders\Enums\ReminderSubjectType;

final class SubjectBuilderFactory
{
    /**
     * @throws \Exception
     */
    public function createBuilder(ReminderSubjectType $type): Builder
    {
        switch ($type) {
            case ReminderSubjectType::DRIVER:
                return Driver::query()->select('id', 'fio as name');
            case ReminderSubjectType::CAR:
                return Car::query()->select('id', 'gos_number as name');
            default:
                throw new \Exception('Unsupported subject type');
        }
    }
}
