<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems;

use Illuminate\Support\Facades\DB;
use Src\Core\Constants\DateFormats;
use Src\Core\ValueObjects\TableItems;
use Src\Reminders\ConditionBuilder\AvailableConditions;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;
use Src\Reminders\Normalizers\ReminderDatabaseNormalizer;

final class GetRemindersTableItemsHandler
{
    /** @var RemindersTableFiltersFactory */
    private $filtersFactory;

    /** @var ReminderDatabaseNormalizer */
    private $normalizer;

    /**
     * @param RemindersTableFiltersFactory $filtersFactory
     * @param ReminderDatabaseNormalizer $normalizer
     */
    public function __construct(RemindersTableFiltersFactory $filtersFactory, ReminderDatabaseNormalizer $normalizer)
    {
        $this->filtersFactory = $filtersFactory;
        $this->normalizer = $normalizer;
    }

    public function handle(GetRemindersTableItemsQuery $query): TableItems
    {
        $builder = DB::table('reminders');
        $availableConditions = [];
        $selectArray = ['reminders.*'];
        foreach (AvailableConditions::AVAILABLE_CONDITIONS as $conditionClass) {
            /** @var class-string<Condition> $conditionClass */
            $condition = $conditionClass::create();
            $condition->addJoin($builder);
            $selectArray = array_merge($selectArray, $condition->getSelectFields());
            $availableConditions[] = $condition;
        }

        $builder = $builder->select($selectArray);

        $filtersPipe = $this->filtersFactory->createFiltersPipe($query->getFilters()->toArray());
        $builder = $filtersPipe->run($builder);

        if ($query->getSortBy() && $query->getSortOrder()) {
            $builder->orderBy($query->getSortBy(), $query->getSortOrder());
        }

        $paginator = $builder->paginate($query->getPerPage(), ['*'], 'page', $query->getPage());

        $items = $paginator
            ->getCollection()
            ->map(function (object $rawReminder) use ($availableConditions) {
                $conditionsViewModels = [];
                foreach ($availableConditions as $condition) {
                    $conditionsViewModels[$condition->getName()] = $condition->makeViewModel((array) $rawReminder);
                }

                $status = ReminderStatus::from($rawReminder->status);
                $action = ReminderAction::from($rawReminder->action);

                $updatedAt = null;
                if ($rawReminder->updated_at) {
                    $updatedAt = \DateTimeImmutable::createFromFormat(DateFormats::SYSTEM_DATETIME, $rawReminder->updated_at)->format(DateFormats::USER_SHOW_DATETIME);
                }

                return [
                    'id' => $rawReminder->id,
                    'title' => $rawReminder->title,
                    'action' => $action->getTitle(),
                    'enabled' => ! $status->equal(ReminderStatus::disable()),
                    'context' => $conditionsViewModels,
                    'updated_at' => $updatedAt,
                ];
            })
            ->toArray();

        return new TableItems(
            $items,
            $paginator->total()
        );
    }
}
