<?php

declare(strict_types=1);

namespace Src\Reminders\Conditions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Reminders\Conditions\BaseConditions\IntCondition;

final class CompanyCondition extends IntCondition
{
    public const TABLE_NAME = 'companies';
    public const FIELD_NAME = 'name';

    protected $conditionName = 'company';

    public function makeViewModel(array $rawReminder): ?ClassifierViewModel
    {
        if (isset($rawReminder['company_id'])) {
            return new ClassifierViewModel(
                $rawReminder['company_id'],
                $rawReminder['company_name'],
            );
        }

        return null;
    }

    public function getSelectFields(): array
    {
        return [
            'companies.id as company_id',
            DB::raw("concat('[', companies.hash_id, '] ', companies.name) as company_name"),
        ];
    }

    public function addJoin(Builder $query): Builder
    {
        return $query
            ->leftJoin('companies', 'companies.id', '=', DB::raw('JSON_EXTRACT(context, "$.'.$this->conditionName.'")'));
    }
}
