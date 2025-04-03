<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;
use Src\Reminders\Enums\ReminderSubjectType;

final class SubjectCondition extends IntCondition
{
    protected $conditionName = 'subject';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        $context = json_decode($rawReminder['context'], true);

        if (isset($context['subject_type'])) {
            if ($context['subject_type'] === ReminderSubjectType::CAR && isset($rawReminder['subject_car_id'])) {
                return new ClassifierViewModel(
                    $rawReminder['subject_car_id'],
                    $rawReminder['subject_car_name'],
                );
            }

            if ($context['subject_type'] === ReminderSubjectType::DRIVER && isset($rawReminder['subject_driver_name'])) {
                return new ClassifierViewModel(
                    $rawReminder['subject_driver_id'],
                    $rawReminder['subject_driver_name'],
                );
            }
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return [
            'subject_drivers.id as subject_driver_id',
            'subject_drivers.fio as subject_driver_name',
            'subject_cars.id as subject_car_id',
            'subject_cars.gos_number as subject_car_name',
        ];
    }

    public function addJoin(Builder $query): Builder
    {
        $query->leftJoin(
            'drivers as subject_drivers',
            'subject_drivers.id',
            '=',
            DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")')
        );

        $query->leftJoin(
            'cars as subject_cars',
            'subject_cars.id',
            '=',
            DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")')
        );

        return $query;
    }
}
