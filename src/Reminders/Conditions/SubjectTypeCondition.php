<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\StringCondition;

final class SubjectTypeCondition extends StringCondition
{
    protected $conditionName = 'subject_type';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        $context = json_decode($rawReminder['context'], true);
        if (isset($context[$this->conditionName])) {
            return new ClassifierViewModel(
                $context[$this->conditionName],
                trans('reminders::subject-types.'.$context[$this->conditionName]),
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return [];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query;
    }
}
